<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Program;
use App\Models\Course;
use App\Models\Semester;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Announcement;
use App\Models\AssessmentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = User::where('role', 'student')->count();
        
        $currentSemester = Semester::current();
        $activeStudents = 0;
        $inactiveStudents = 0;
        
        if ($currentSemester) {
            $activeStudents = User::where('role', 'student')
                ->whereHas('enrollments', function($query) use ($currentSemester) {
                    $query->whereIn('status', ['enrolled', 'pending_payment'])
                          ->whereHas('courseSection', function($q) use ($currentSemester) {
                              $q->where('semester_id', $currentSemester->id);
                          });
                })
                ->count();
            
            $inactiveStudents = $totalStudents - $activeStudents;
        }

        $totalProfessors = User::where('role', 'professor')->count();
        $totalCourses = Course::count();
        $totalPrograms = Program::count();
        $totalDepartments = Department::count();
        
        // Payment Analytics
        $totalPayments = Payment::sum('total_amount');
        $totalPaid = Payment::sum('amount_paid');
        $totalBalance = Payment::sum('balance');
        
        $recentEnrollments = Enrollment::with(['student', 'courseSection.course'])
            ->latest()
            ->take(10)
            ->get();

        $announcements = Announcement::active()->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'activeStudents',
            'inactiveStudents',
            'totalProfessors',
            'totalCourses',
            'totalPrograms',
            'totalDepartments',
            'totalPayments',
            'totalPaid',
            'totalBalance',
            'recentEnrollments',
            'announcements'
        ));
    }

    // Professor Management
    public function professors(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'active');

        $query = User::where('role', 'professor');

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'deactivated') {
            $query->where('is_active', false);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $professors = $query->withCount('courseSections')
            ->orderBy('name')
            ->paginate(20);
        
        return view('admin.professors.index', compact('professors', 'search', 'status'));
    }

    public function storeProfessor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users,email',
        ]);

        // Generate random temporary password
        $tempPassword = 'temp' . rand(1000, 9999);
        
        $validated['password'] = bcrypt($tempPassword);
        $validated['role'] = 'professor';
        $validated['must_change_password'] = true;
        $validated['temp_password'] = $tempPassword; // Store for admin viewing

        $professor = User::create($validated);
        
        return redirect()->route('admin.professors')
            ->with('success', "Professor added successfully! Temporary password: <strong>{$tempPassword}</strong> (You can view this later in the Actions menu)");
    }

    public function updateProfessor(Request $request, User $professor)
    {
        if ($professor->role !== 'professor') {
            abort(403, 'Invalid professor');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $professor->id,
            'email' => 'required|email|unique:users,email,' . $professor->id,
        ]);

        $professor->update($validated);
        
        return redirect()->route('admin.professors')->with('success', 'Professor updated successfully');
    }

    public function deactivateProfessor(Request $request, User $professor)
    {
        if ($professor->role !== 'professor') {
            abort(403, 'Invalid professor');
        }

        $request->validate([
            'deactivation_reason' => 'required|string|max:500',
        ]);

        $professor->update([
            'is_active' => false,
            'deactivation_reason' => $request->deactivation_reason,
        ]);
        
        return redirect()->route('admin.professors')->with('success', 'Professor deactivated successfully');
    }

    public function activateProfessor(User $professor)
    {
        if ($professor->role !== 'professor') {
            abort(403, 'Invalid professor');
        }

        $professor->update([
            'is_active' => true,
            'deactivation_reason' => null,
        ]);

        return redirect()->route('admin.professors')->with('success', 'Professor activated successfully');
    }

    // Department Management
    public function departments(Request $request)
    {
        $search = $request->input('search');
        
        $query = Department::withCount('programs');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $departments = $query->get();
        return view('admin.departments.index', compact('departments', 'search'));
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'required|string|max:10|unique:departments,code',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);
        return redirect()->route('admin.departments')->with('success', 'Department created successfully');
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:10|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $department->update($validated);
        return redirect()->route('admin.departments')->with('success', 'Department updated successfully');
    }

    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments')->with('success', 'Department deleted successfully');
    }

    // Program Management
    public function programs(Request $request)
    {
        $search = $request->input('search');

        $query = Program::with('department');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $programs = $query->get();
        $departments = Department::all();
        return view('admin.programs.index', compact('programs', 'departments', 'search'));
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255|unique:programs,name',
            'code' => 'required|string|max:10|unique:programs,code',
            'min_units' => 'required|integer|min:1',
            'max_units' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Program::create($validated);
        return redirect()->route('admin.programs')->with('success', 'Program created successfully');
    }

    public function updateProgram(Request $request, Program $program)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255|unique:programs,name,' . $program->id,
            'code' => 'required|string|max:10|unique:programs,code,' . $program->id,
            'min_units' => 'required|integer|min:1',
            'max_units' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $program->update($validated);
        return redirect()->route('admin.programs')->with('success', 'Program updated successfully');
    }

    public function destroyProgram(Program $program)
    {
        $program->delete();
        return redirect()->route('admin.programs')->with('success', 'Program deleted successfully');
    }

    // Course Management
    public function courses(Request $request)
    {
        $search = $request->input('search');

        // Fetch programs with their courses, filtering courses if search exists
        $programsQuery = Program::query();

        if ($search) {
            $programsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('courses', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('course_code', 'like', "%{$search}%");
                  });
            });
        }

        $programsWithCourses = $programsQuery->with(['courses' => function($q) use ($search, $request) {
            if ($request->has('archived')) {
                $q->onlyTrashed();
            }
            if ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%");
            }
        }])->get();

        // Also fetch courses that don't belong to any program
        $uncategorizedCoursesQuery = Course::doesntHave('programs');
        if ($request->has('archived')) {
            $uncategorizedCoursesQuery->onlyTrashed();
        }
        if ($search) {
            $uncategorizedCoursesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%");
            });
        }
        $uncategorizedCourses = $uncategorizedCoursesQuery->get();

        // All programs for the dropdown in "Add Course" modal
        $allPrograms = Program::all();
        
        return view('admin.courses.index', compact('programsWithCourses', 'uncategorizedCourses', 'allPrograms', 'search'));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code',
            'name' => 'required|string|max:255|unique:courses,name',
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1',
            'is_general' => 'boolean',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,id',
        ]);

        $course = Course::create([
            'course_code' => $validated['course_code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'units' => $validated['units'],
            'is_general' => $validated['is_general'] ?? false,
        ]);

        if (!empty($validated['programs'])) {
            $course->programs()->attach($validated['programs']);
        }

        return redirect()->route('admin.courses')->with('success', 'Course created successfully');
    }

    public function updateCourse(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:20|unique:courses,course_code,' . $course->id,
            'name' => 'required|string|max:255|unique:courses,name,' . $course->id,
            'description' => 'nullable|string',
            'units' => 'required|integer|min:1',
            'is_general' => 'boolean',
            'programs' => 'nullable|array',
            'programs.*' => 'exists:programs,id',
        ]);

        $course->update([
            'course_code' => $validated['course_code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'units' => $validated['units'],
            'is_general' => $validated['is_general'] ?? false,
        ]);

        $course->programs()->sync($validated['programs'] ?? []);

        return redirect()->route('admin.courses')->with('success', 'Course updated successfully');
    }

    public function destroyCourse(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully');
    }

    // Semester Management
    public function semesters(Request $request)
    {
        $search = $request->input('search');
        
        $query = Semester::orderBy('start_date', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $semesters = $query->get();
        return view('admin.semesters.index', compact('semesters', 'search'));
    }

    public function storeSemester(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:semesters,name',
            'code' => 'required|string|max:20|unique:semesters,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        if ($validated['is_current'] ?? false) {
            Semester::where('is_current', true)->update(['is_current' => false]);
        }

        Semester::create($validated);
        return redirect()->route('admin.semesters')->with('success', 'Semester created successfully');
    }

    public function setCurrentSemester(Semester $semester)
    {
        Semester::where('is_current', true)->update(['is_current' => false]);
        $semester->update(['is_current' => true]);
        return redirect()->route('admin.semesters')->with('success', 'Current semester updated');
    }

    // Course Section Management
    public function courseSections(Request $request)
    {
        $search = $request->input('search');

        $query = CourseSection::with(['course', 'professor', 'semester']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('section_code', 'like', "%{$search}%")
                  ->orWhereHas('course', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('course_code', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $sections = $query->get();
        $courses = Course::all();
        $professors = User::where('role', 'professor')->get();
        $semesters = Semester::all();
        return view('admin.course-sections.index', compact('sections', 'courses', 'professors', 'semesters', 'search'));
    }

    public function storeCourseSection(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'professor_id' => 'nullable|exists:users,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_code' => 'required|string|max:10|unique:course_sections,section_code',
            'max_students' => 'required|integer|min:1',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string',
        ]);

        CourseSection::create($validated);
        return redirect()->route('admin.course-sections')->with('success', 'Course section created successfully');
    }

    public function updateCourseSection(Request $request, CourseSection $section)
    {
        $validated = $request->validate([
            'professor_id' => 'nullable|exists:users,id',
            'section_code' => 'required|string|max:10|unique:course_sections,section_code,' . $section->id,
            'max_students' => 'required|integer|min:1',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string',
            'grades_visible' => 'nullable|boolean',
        ]);

        // Handle checkbox - if not present, set to false
        $validated['grades_visible'] = $request->has('grades_visible') ? true : false;

        $section->update($validated);
        return redirect()->route('admin.course-sections')->with('success', 'Course section updated successfully');
    }

    public function destroyCourseSection(CourseSection $section)
    {
        $section->delete();
        return redirect()->route('admin.course-sections')->with('success', 'Course section deleted successfully');
    }

    // Enrollment Management
    public function enrollments(Request $request)
    {
        $search = $request->input('search');

        $query = Enrollment::with(['student', 'courseSection.course', 'courseSection.semester'])
            ->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('courseSection.course', function($sq) use ($search) {
                    $sq->where('course_code', 'like', "%{$search}%")
                       ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $enrollments = $query->paginate(50);
        
        $students = User::where('role', 'student')->get();
        $sections = CourseSection::with(['course', 'semester'])->get();
        
        return view('admin.enrollments.index', compact('enrollments', 'students', 'sections', 'search'));
    }

    public function storeEnrollment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_section_id' => 'required|exists:course_sections,id',
            'status' => 'required|in:pending_payment,enrolled,dropped',
        ]);

        Enrollment::create($validated);
        return redirect()->route('admin.enrollments')->with('success', 'Student enrolled successfully');
    }

    // Payment Management
    public function payments(Request $request)
    {
        $search = $request->input('search');
        
        $query = Payment::with(['student', 'semester'])->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate(50);
        return view('admin.payments.index', compact('payments', 'search'));
    }

    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
        ]);

        $payment->amount_paid = $validated['amount_paid'];
        $payment->payment_method = $validated['payment_method'];
        $payment->reference_number = $validated['reference_number'];
        
        if ($payment->amount_paid >= $payment->total_amount) {
            $payment->paid_at = now();
            
            // Update all enrollments for this student/semester to 'enrolled'
            Enrollment::where('student_id', $payment->student_id)
                ->whereHas('courseSection', function($query) use ($payment) {
                    $query->where('semester_id', $payment->semester_id);
                })
                ->update(['status' => 'enrolled']);
        }
        
        $payment->updateBalance();

        return redirect()->route('admin.payments')->with('success', 'Payment updated successfully');
    }

    // Student Management
    public function students(Request $request)
    {
        $search = $request->input('search');

        $query = User::where('role', 'student')->with('program');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(50);
        
        return view('admin.students.index', compact('students', 'search'));
    }

    // Announcement Management
    public function announcements(Request $request)
    {
        $search = $request->input('search');
        
        $query = Announcement::latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $announcements = $query->get();
        return view('admin.announcements.index', compact('announcements', 'search'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all,students,professors',
            'is_active' => 'boolean',
        ]);

        Announcement::create($validated);
        return redirect()->route('admin.announcements')->with('success', 'Announcement created successfully');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all,students,professors',
            'is_active' => 'boolean',
        ]);

        $announcement->update($validated);
        return redirect()->route('admin.announcements')->with('success', 'Announcement updated successfully');
    }

    public function destroyAnnouncement(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements')->with('success', 'Announcement deleted successfully');
    }

    public function destroyEnrollment(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('admin.enrollments')->with('success', 'Enrollment archived successfully');
    }

    public function dropEnrollment(Enrollment $enrollment)
    {
        $enrollment->update(['status' => 'dropped']);
        
        $this->recalculatePayment($enrollment->student_id, $enrollment->courseSection->semester_id);
        
        return redirect()->route('admin.enrollments')->with('success', 'Course dropped successfully');
    }

    public function undropEnrollment(Enrollment $enrollment)
    {
        // Restore to 'pending_payment' initially to require payment/verification
        $enrollment->update(['status' => 'pending_payment']);
        
        $this->recalculatePayment($enrollment->student_id, $enrollment->courseSection->semester_id);
        
        // Check if student is fully paid after recalculation, if so, update to 'enrolled'
        $payment = Payment::where('student_id', $enrollment->student_id)
            ->where('semester_id', $enrollment->courseSection->semester_id)
            ->first();
            
        if ($payment && $payment->balance <= 0 && $payment->total_amount > 0) {
             $enrollment->update(['status' => 'enrolled']);
        }
        
        return redirect()->route('admin.enrollments')->with('success', 'Course restoration successful');
    }

    private function recalculatePayment($studentId, $semesterId)
    {
        $student = User::find($studentId);
        if (!$student) return;

        $totalUnits = $student->getTotalEnrolledUnits($semesterId);
        
        $payment = Payment::where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->first();
            
        if ($payment) {
            // If no units enrolled (e.g. all dropped), assessment should be 0 based on recent logic
            if ($totalUnits == 0) {
                $payment->total_amount = 0;
                $payment->balance = 0 - $payment->amount_paid; // Negative balance means refund needed? Or just 0 if not paid. 
                // Actually balance = total - paid. If total is 0, balance = -paid.
            } else {
                // Calculate total amount using assessment fee structure matching StudentController
                $perUnitFee = 640.00; // Base per unit fee
                $perUnitTotal = $perUnitFee * $totalUnits;
                
                // Get all active miscellaneous assessment fees
                $assessmentFees = AssessmentFee::where('is_active', true)
                    ->whereNull('course') // Only miscellaneous fees
                    ->sum('amount');
                
                $totalAmount = $perUnitTotal + $assessmentFees;
                
                $payment->total_amount = $totalAmount;
                $payment->balance = $totalAmount - $payment->amount_paid;
            }
            
            // Update status
            if ($payment->balance <= 0 && $payment->total_amount > 0) {
                $payment->status = 'paid';
            } elseif ($payment->amount_paid > 0) {
                $payment->status = 'partial';
            } else {
                $payment->status = 'pending';
            }
            
            $payment->save();
        }
    }

    // Restore methods
    public function restoreDepartment($id)
    {
        Department::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.departments')->with('success', 'Department restored successfully');
    }

    public function restoreProgram($id)
    {
        Program::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.programs')->with('success', 'Program restored successfully');
    }

    public function restoreCourse($id)
    {
        Course::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.courses')->with('success', 'Course restored successfully');
    }

    public function restoreSemester($id)
    {
        Semester::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.semesters')->with('success', 'Semester restored successfully');
    }

    public function restoreCourseSection($id)
    {
        CourseSection::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.course-sections')->with('success', 'Course section restored successfully');
    }

    public function restoreEnrollment($id)
    {
        Enrollment::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.enrollments')->with('success', 'Enrollment restored successfully');
    }

    public function restoreAnnouncement($id)
    {
        Announcement::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.announcements')->with('success', 'Announcement restored successfully');
    }

    // Assessment Fee Management
    public function assessments(Request $request)
    {
        $search = $request->input('search');
        
        $query = AssessmentFee::orderBy('order');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('charge_description', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
            });
        }

        if ($request->has('archived')) {
            $query->onlyTrashed();
        }

        $assessments = $query->get();
        return view('admin.assessments.index', compact('assessments', 'search'));
    }

    public function storeAssessment(Request $request)
    {
        $validated = $request->validate([
            'charge_description' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'order' => 'nullable|integer|min:0',
        ]);

        AssessmentFee::create($validated);
        return redirect()->route('admin.assessments')->with('success', 'Assessment fee created successfully');
    }

    public function updateAssessment(Request $request, AssessmentFee $assessment)
    {
        $validated = $request->validate([
            'charge_description' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'order' => 'nullable|integer|min:0',
        ]);

        $assessment->update($validated);
        return redirect()->route('admin.assessments')->with('success', 'Assessment fee updated successfully');
    }

    public function destroyAssessment(AssessmentFee $assessment)
    {
        $assessment->delete();
        return redirect()->route('admin.assessments')->with('success', 'Assessment fee deleted successfully');
    }

    public function restoreAssessment($id)
    {
        AssessmentFee::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.assessments')->with('success', 'Assessment fee restored successfully');
    }
}
