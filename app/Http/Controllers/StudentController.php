<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Payment;
use App\Models\Announcement;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = auth()->user();
        $currentSemester = Semester::current();
        
        $enrolledCourses = collect();
        $totalUnits = 0;
        $payment = null;
        
        if ($currentSemester) {
            $enrolledCourses = $student->enrollments()
                ->whereHas('courseSection', function($query) use ($currentSemester) {
                    $query->where('semester_id', $currentSemester->id);
                })
                ->with('courseSection.course')
                ->get();
            
            $totalUnits = $student->getTotalEnrolledUnits($currentSemester->id);
            
            $payment = Payment::where('student_id', $student->id)
                ->where('semester_id', $currentSemester->id)
                ->first();
        }

        $announcements = Announcement::active()
            ->forRole('student')
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'currentSemester',
            'enrolledCourses',
            'totalUnits',
            'payment',
            'announcements'
        ));
    }

    public function enrollmentForm()
    {
        $student = auth()->user();
        $currentSemester = Semester::current();
        
        if (!$currentSemester) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No active semester available for enrollment');
        }

        // Get already enrolled sections
        $enrolledSectionIds = $student->enrollments()
            ->whereHas('courseSection', function($query) use ($currentSemester) {
                $query->where('semester_id', $currentSemester->id);
            })
            ->pluck('course_section_id')
            ->toArray();

        // Get available course sections
        // Priority: Program-specific courses + General Education courses
        $availableSections = CourseSection::where('semester_id', $currentSemester->id)
            ->whereNotIn('id', $enrolledSectionIds)
            ->with(['course.programs', 'professor'])
            ->get()
            ->filter(function($section) use ($student) {
                // Include if:
                // 1. Course is general education, OR
                // 2. Course is linked to student's program
                if ($section->course->is_general) {
                    return true;
                }
                
                if ($student->program_id) {
                    return $section->course->programs->contains('id', $student->program_id);
                }
                
                return false;
            })
            ->filter(function($section) {
                // Only show sections with available slots
                return $section->hasAvailableSlots();
            })
            ->groupBy(function($section) {
                return $section->course->is_general ? 'General Education' : 'Major Courses';
            });

        $currentUnits = $student->getTotalEnrolledUnits($currentSemester->id);
        $program = $student->program;

        return view('student.enrollment', compact(
            'availableSections',
            'currentSemester',
            'currentUnits',
            'program'
        ));
    }

    public function enroll(Request $request)
    {
        $student = auth()->user();
        $currentSemester = Semester::current();
        
        if (!$currentSemester) {
            return back()->with('error', 'No active semester available');
        }

        $validated = $request->validate([
            'course_sections' => 'required|array|min:1',
            'course_sections.*' => 'exists:course_sections,id',
        ]);

        DB::beginTransaction();
        
        try {
            // Calculate total units for selected courses
            $selectedSections = CourseSection::whereIn('id', $validated['course_sections'])
                ->with('course')
                ->get();
            
            $newUnits = $selectedSections->sum(function($section) {
                return $section->course->units;
            });
            
            // Get current enrolled units
            $currentUnits = $student->getTotalEnrolledUnits($currentSemester->id);
            $totalUnits = $currentUnits + $newUnits;
            
            // Validate against program unit limits
            if ($student->program) {
                if ($totalUnits < $student->program->min_units) {
                    DB::rollBack();
                    return back()->with('error', 
                        "Total units ({$totalUnits}) is below the minimum required ({$student->program->min_units} units). Please select more courses."
                    );
                }
                
                if ($totalUnits > $student->program->max_units) {
                    DB::rollBack();
                    return back()->with('error', 
                        "Total units ({$totalUnits}) exceeds the maximum allowed ({$student->program->max_units} units). Please remove some courses."
                    );
                }
            }
            
            // Check for section capacity
            foreach ($selectedSections as $section) {
                if (!$section->hasAvailableSlots()) {
                    DB::rollBack();
                    return back()->with('error', 
                        "Section {$section->full_code} is already full. Please select a different section."
                    );
                }
            }
            
            // Check for schedule conflicts
            // Get currently enrolled sections for this semester
            $currentEnrollments = $student->enrollments()
                ->whereHas('courseSection', function($query) use ($currentSemester) {
                    $query->where('semester_id', $currentSemester->id);
                })
                ->with('courseSection')
                ->whereIn('status', ['enrolled', 'pending_payment'])
                ->get();
            
            // Check new sections against current enrollments
            foreach ($selectedSections as $newSection) {
                foreach ($currentEnrollments as $enrollment) {
                    $existingSection = $enrollment->courseSection;
                    
                    if (\App\Helpers\ScheduleHelper::hasConflict($newSection->schedule, $existingSection->schedule)) {
                        DB::rollBack();
                        return back()->with('error', 
                            "Schedule conflict detected! {$newSection->full_code} ({$newSection->schedule}) conflicts with {$existingSection->full_code} ({$existingSection->schedule}). Please choose a different section."
                        );
                    }
                }
            }
            
            // Check for conflicts among newly selected sections
            for ($i = 0; $i < count($selectedSections); $i++) {
                for ($j = $i + 1; $j < count($selectedSections); $j++) {
                    if (\App\Helpers\ScheduleHelper::hasConflict($selectedSections[$i]->schedule, $selectedSections[$j]->schedule)) {
                        DB::rollBack();
                        return back()->with('error', 
                            "Schedule conflict detected! {$selectedSections[$i]->full_code} ({$selectedSections[$i]->schedule}) conflicts with {$selectedSections[$j]->full_code} ({$selectedSections[$j]->schedule}). Please choose different sections."
                        );
                    }
                }
            }
            
            // Create enrollments
            foreach ($validated['course_sections'] as $sectionId) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_section_id' => $sectionId,
                    'status' => 'pending_payment',
                ]);
            }
            
            // Create or update payment record
            $payment = Payment::firstOrNew([
                'student_id' => $student->id,
                'semester_id' => $currentSemester->id,
            ]);
            
            // Calculate total amount (1,500 PHP per unit)
            $pricePerUnit = 1500;
            $totalAmount = $totalUnits * $pricePerUnit;
            
            $payment->total_amount = $totalAmount;
            $payment->balance = $totalAmount - ($payment->amount_paid ?? 0);
            $payment->status = $payment->amount_paid > 0 ? 'partial' : 'pending';
            $payment->save();
            
            DB::commit();
            
            return redirect()->route('student.dashboard')
                ->with('success', 'Enrollment successful! Please proceed to payment.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Enrollment failed: ' . $e->getMessage());
        }
    }

    public function myEnrollment()
    {
        $student = auth()->user();
        $currentSemester = Semester::current();
        
        $enrollments = collect();
        $totalUnits = 0;
        
        if ($currentSemester) {
            $enrollments = $student->enrollments()
                ->whereHas('courseSection', function($query) use ($currentSemester) {
                    $query->where('semester_id', $currentSemester->id);
                })
                ->with(['courseSection.course', 'courseSection.professor'])
                ->get();
            
            $totalUnits = $student->getTotalEnrolledUnits($currentSemester->id);
        }

        return view('student.my-enrollment', compact('enrollments', 'totalUnits', 'currentSemester'));
    }

    public function dropCourse(Enrollment $enrollment)
    {
        // Ensure student owns this enrollment
        if ($enrollment->student_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $enrollment->update(['status' => 'dropped']);
        
        return redirect()->route('student.my-enrollment')
            ->with('success', 'Course dropped successfully');
    }

    public function payments()
    {
        $student = auth()->user();
        $payments = Payment::where('student_id', $student->id)
            ->with('semester')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.payments', compact('payments'));
    }

    public function showPayment(Payment $payment)
    {
        // Ensure student owns this payment
        if ($payment->student_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $enrollments = Enrollment::where('student_id', $payment->student_id)
            ->whereHas('courseSection', function($query) use ($payment) {
                $query->where('semester_id', $payment->semester_id);
            })
            ->with('courseSection.course')
            ->get();

        return view('student.payment-details', compact('payment', 'enrollments'));
    }

    public function processPayment(Request $request, Payment $payment)
    {
        // Ensure student owns this payment
        if ($payment->student_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // This is a placeholder for online payment integration
        // In a real system, you would integrate with a payment gateway here
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            $payment->amount_paid += $validated['amount'];
            $payment->payment_method = $validated['payment_method'];
            $payment->reference_number = 'REF-' . time() . '-' . $payment->id;
            
            if ($payment->amount_paid >= $payment->total_amount) {
                $payment->paid_at = now();
                
                // Update all enrollments to 'enrolled'
                Enrollment::where('student_id', $payment->student_id)
                    ->whereHas('courseSection', function($query) use ($payment) {
                        $query->where('semester_id', $payment->semester_id);
                    })
                    ->update(['status' => 'enrolled']);
            }
            
            $payment->updateBalance();
            
            DB::commit();
            
            return redirect()->route('student.payments')
                ->with('success', 'Payment processed successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function grades()
    {
        $student = auth()->user();
        
        $enrollments = $student->enrollments()
            ->with(['courseSection.course', 'courseSection.semester'])
            ->whereNotNull('grade')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($enrollment) {
                return $enrollment->courseSection->semester->name;
            });

        return view('student.grades', compact('enrollments'));
    }
}
