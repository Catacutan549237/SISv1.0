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
    
    // Programs
    Route::get('/programs', [AdminController::class, 'programs'])->name('programs');
    Route::post('/programs', [AdminController::class, 'storeProgram'])->name('programs.store');
    Route::put('/programs/{program}', [AdminController::class, 'updateProgram'])->name('programs.update');
    Route::delete('/programs/{program}', [AdminController::class, 'destroyProgram'])->name('programs.destroy');
    
    // Courses
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::put('/courses/{course}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminController::class, 'destroyCourse'])->name('courses.destroy');
    
    // Semesters
    Route::get('/semesters', [AdminController::class, 'semesters'])->name('semesters');
    Route::post('/semesters', [AdminController::class, 'storeSemester'])->name('semesters.store');
    Route::post('/semesters/{semester}/set-current', [AdminController::class, 'setCurrentSemester'])->name('semesters.set-current');
    
    // Course Sections
    Route::get('/course-sections', [AdminController::class, 'courseSections'])->name('course-sections');
    Route::post('/course-sections', [AdminController::class, 'storeCourseSection'])->name('course-sections.store');
    Route::put('/course-sections/{section}', [AdminController::class, 'updateCourseSection'])->name('course-sections.update');
    Route::delete('/course-sections/{section}', [AdminController::class, 'destroyCourseSection'])->name('course-sections.destroy');
    
    // Enrollments
    Route::get('/enrollments', [AdminController::class, 'enrollments'])->name('enrollments');
    Route::post('/enrollments', [AdminController::class, 'storeEnrollment'])->name('enrollments.store');
    
    // Payments
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::put('/payments/{payment}', [AdminController::class, 'updatePaymentStatus'])->name('payments.update');
    
    // Students
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    
    // Professors
    Route::get('/professors', [AdminController::class, 'professors'])->name('professors');
    Route::post('/professors', [AdminController::class, 'storeProfessor'])->name('professors.store');
    Route::put('/professors/{professor}', [AdminController::class, 'updateProfessor'])->name('professors.update');
    Route::delete('/professors/{professor}', [AdminController::class, 'destroyProfessor'])->name('professors.destroy');
    
    // Announcements
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
    Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AdminController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminController::class, 'destroyAnnouncement'])->name('announcements.destroy');
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
    Route::get('/payments', [StudentController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [StudentController::class, 'showPayment'])->name('payments.show');
    Route::post('/payments/{payment}/process', [StudentController::class, 'processPayment'])->name('payments.process');
    Route::get('/grades', [StudentController::class, 'grades'])->name('grades');
});
