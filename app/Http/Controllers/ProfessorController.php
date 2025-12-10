<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\Announcement;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function dashboard()
    {
        $professor = auth()->user();
        
        // Redirect to password change if required
        if ($professor->must_change_password) {
            return redirect()->route('professor.password.change')
                ->with('warning', 'You must change your temporary password before continuing.');
        }
        
        $currentSemester = Semester::current();
        
        $assignedSections = collect();
        $totalStudents = 0;
        
        if ($currentSemester) {
            $assignedSections = CourseSection::where('professor_id', $professor->id)
                ->where('semester_id', $currentSemester->id)
                ->with(['course', 'enrollments.student'])
                ->get();
            
            $totalStudents = $assignedSections->sum(function($section) {
                return $section->enrollments()->whereIn('status', ['enrolled', 'pending_payment'])->count();
            });
        }

        $announcements = Announcement::active()
            ->forRole('professor')
            ->latest()
            ->take(5)
            ->get();

        return view('professor.dashboard', compact(
            'assignedSections',
            'totalStudents',
            'currentSemester',
            'announcements'
        ));
    }

    public function myCourses(Request $request)
    {
        $professor = auth()->user();
        $currentSemester = Semester::current();
        $search = $request->input('search');
        
        $query = CourseSection::where('professor_id', $professor->id)
            ->where('semester_id', $currentSemester->id)
            ->with(['course', 'semester', 'enrollments' => function($query) {
                $query->whereIn('status', ['enrolled', 'pending_payment']);
            }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('section_code', 'like', "%{$search}%")
                  ->orWhereHas('course', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('course_code', 'like', "%{$search}%");
                  });
            });
        }

        $sections = $query->get();

        return view('professor.courses', compact('sections', 'currentSemester', 'search'));
    }

    public function showSection(CourseSection $section)
    {
        // Ensure professor owns this section
        if ($section->professor_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $enrollments = $section->enrollments()
            ->with('student')
            ->whereIn('status', ['enrolled', 'pending_payment'])
            ->get();

        return view('professor.section-details', compact('section', 'enrollments'));
    }

    public function grades(Request $request, CourseSection $section)
    {
        // Ensure professor owns this section
        if ($section->professor_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $search = $request->input('search');

        $query = $section->enrollments()
            ->with('student')
            ->whereIn('status', ['enrolled', 'pending_payment']);

        if ($search) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('student_id')->get();

        return view('professor.grades', compact('section', 'enrollments', 'search'));
    }

    public function updateGrades(Request $request, CourseSection $section)
    {
        // Ensure professor owns this section
        if ($section->professor_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['grades'] as $enrollmentId => $grade) {
            $enrollment = Enrollment::find($enrollmentId);
            if ($enrollment && $enrollment->course_section_id === $section->id) {
                $enrollment->update(['grade' => $grade]);
            }
        }

        return redirect()->route('professor.courses')
            ->with('success', 'Grades saved successfully');
    }

    public function showChangePassword()
    {
        return view('professor.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $professor = auth()->user();

        // Verify current password
        if (!password_verify($validated['current_password'], $professor->password)) {
            return back()->with('error', 'Current password is incorrect');
        }

        // Update password and remove must_change_password flag
        $professor->update([
            'password' => bcrypt($validated['password']),
            'must_change_password' => false,
            'temp_password' => null, // Clear temp password
        ]);

        return redirect()->route('professor.dashboard')
            ->with('success', 'Password changed successfully!');
    }
}
