<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Departments
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}', [AdminController::class, 'destroyDepartment'])->name('departments.destroy');
    Route::post('/departments/{id}/restore', [AdminController::class, 'restoreDepartment'])->name('departments.restore');
    
    // Programs
    Route::get('/programs', [AdminController::class, 'programs'])->name('programs');
    Route::post('/programs', [AdminController::class, 'storeProgram'])->name('programs.store');
    Route::put('/programs/{program}', [AdminController::class, 'updateProgram'])->name('programs.update');
    Route::delete('/programs/{program}', [AdminController::class, 'destroyProgram'])->name('programs.destroy');
    Route::post('/programs/{id}/restore', [AdminController::class, 'restoreProgram'])->name('programs.restore');
    
    // Courses
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::put('/courses/{course}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminController::class, 'destroyCourse'])->name('courses.destroy');
    Route::post('/courses/{id}/restore', [AdminController::class, 'restoreCourse'])->name('courses.restore');
    
    // Semesters
    Route::get('/semesters', [AdminController::class, 'semesters'])->name('semesters');
    Route::post('/semesters', [AdminController::class, 'storeSemester'])->name('semesters.store');
    Route::post('/semesters/{semester}/set-current', [AdminController::class, 'setCurrentSemester'])->name('semesters.set-current');
    Route::delete('/semesters/{semester}', [AdminController::class, 'destroySemester'])->name('semesters.destroy');
    Route::post('/semesters/{id}/restore', [AdminController::class, 'restoreSemester'])->name('semesters.restore');
    
    // Course Sections
    Route::get('/course-sections', [AdminController::class, 'courseSections'])->name('course-sections');
    Route::post('/course-sections', [AdminController::class, 'storeCourseSection'])->name('course-sections.store');
    Route::put('/course-sections/{section}', [AdminController::class, 'updateCourseSection'])->name('course-sections.update');
    Route::delete('/course-sections/{section}', [AdminController::class, 'destroyCourseSection'])->name('course-sections.destroy');
    Route::post('/course-sections/{id}/restore', [AdminController::class, 'restoreCourseSection'])->name('course-sections.restore');
    
    // Enrollments
    Route::get('/enrollments', [AdminController::class, 'enrollments'])->name('enrollments');
    Route::post('/enrollments', [AdminController::class, 'storeEnrollment'])->name('enrollments.store');
    Route::put('/enrollments/{enrollment}/drop', [AdminController::class, 'dropEnrollment'])->name('enrollments.drop');
    Route::put('/enrollments/{enrollment}/undrop', [AdminController::class, 'undropEnrollment'])->name('enrollments.undrop');
    Route::delete('/enrollments/{enrollment}', [AdminController::class, 'destroyEnrollment'])->name('enrollments.destroy');
    Route::post('/enrollments/{id}/restore', [AdminController::class, 'restoreEnrollment'])->name('enrollments.restore');
    
    // Payments
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::put('/payments/{payment}', [AdminController::class, 'updatePaymentStatus'])->name('payments.update');
    
    // Students
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    
    // Professors
    Route::get('/professors', [AdminController::class, 'professors'])->name('professors');
    Route::post('/professors', [AdminController::class, 'storeProfessor'])->name('professors.store');
    Route::put('/professors/{professor}', [AdminController::class, 'updateProfessor'])->name('professors.update');
    Route::post('/professors/{professor}/deactivate', [AdminController::class, 'deactivateProfessor'])->name('professors.deactivate');
    Route::post('/professors/{professor}/activate', [AdminController::class, 'activateProfessor'])->name('professors.activate');
    
    // Announcements
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AdminController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');
    Route::post('/announcements/{id}/restore', [AdminController::class, 'restoreAnnouncement'])->name('announcements.restore');
    
    // Assessment Fees
    Route::get('/assessments', [AdminController::class, 'assessments'])->name('assessments');
    Route::post('/assessments', [AdminController::class, 'storeAssessment'])->name('assessments.store');
    Route::put('/assessments/{assessment}', [AdminController::class, 'updateAssessment'])->name('assessments.update');
    Route::delete('/assessments/{assessment}', [AdminController::class, 'destroyAssessment'])->name('assessments.destroy');
    Route::post('/assessments/{id}/restore', [AdminController::class, 'restoreAssessment'])->name('assessments.restore');
});

// Professor routes
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    // Password change routes (accessible even with must_change_password flag)
    Route::get('/change-password', [ProfessorController::class, 'showChangePassword'])->name('password.change');
    Route::put('/change-password', [ProfessorController::class, 'updatePassword'])->name('password.update');
    
    Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses', [ProfessorController::class, 'myCourses'])->name('courses');
    Route::get('/sections/{section}', [ProfessorController::class, 'showSection'])->name('sections.show');
    Route::get('/sections/{section}/grades', [ProfessorController::class, 'grades'])->name('grades');
    Route::post('/sections/{section}/grades', [ProfessorController::class, 'updateGrades'])->name('grades.update');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/enrollment', [StudentController::class, 'enrollmentForm'])->name('enrollment');
    Route::post('/enrollment', [StudentController::class, 'enroll'])->name('enrollment.store');
    Route::get('/my-enrollment', [StudentController::class, 'myEnrollment'])->name('my-enrollment');
    Route::post('/enrollments/{enrollment}/drop', [StudentController::class, 'dropCourse'])->name('enrollment.drop');
    Route::get('/assessments', [StudentController::class, 'assessments'])->name('assessments');
    Route::get('/payments', [StudentController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [StudentController::class, 'showPayment'])->name('payments.show');
    Route::post('/payments/{payment}/process', [StudentController::class, 'processPayment'])->name('payments.process');
    Route::get('/grades', [StudentController::class, 'grades'])->name('grades');
    Route::get('/change-password', [StudentController::class, 'showChangePassword'])->name('password.change');
    Route::put('/change-password', [StudentController::class, 'updatePassword'])->name('password.update');
});
