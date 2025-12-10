@extends('layouts.dashboard')

@section('title', 'Student Dashboard')

@section('sidebar')
<div class="nav-item">
    <a href="{{ route('student.dashboard') }}" class="nav-link active">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.enrollment') }}" class="nav-link">
        <span class="nav-icon">📝</span>
        <span>Enroll Course</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.my-enrollment') }}" class="nav-link">
        <span class="nav-icon">📚</span>
        <span>Class Schedule</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.assessments') }}" class="nav-link">
        <span class="nav-icon">📋</span>
        <span>Assessment</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.payments') }}" class="nav-link">
        <span class="nav-icon">💳</span>
        <span>Online Payment</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.grades') }}" class="nav-link">
        <span class="nav-icon">🎓</span>
        <span>Evaluation</span>
    </a>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $student->student_id }}</h1>
    <p class="page-subtitle">{{ $student->name }}</p>
    <p class="page-subtitle">{{ $student->email }}</p>
    @if($student->program)
        <p class="page-subtitle" style="font-weight: 600; color: var(--sage-green);">
            {{ $currentSemester ? $currentSemester->name : 'No Active Semester' }}
        </p>
        <p class="page-subtitle">{{ $student->year_level }} {{ $student->program->name }}</p>
    @endif
</div>

<!-- Stats Cards -->
<div class="card-grid">
    @php
        $totalExams = 8;
        $totalAssessment = $payment ? $payment->total_amount : 0;
        $totalPaid = $payment ? $payment->amount_paid : 0;
        $perExam = $totalAssessment > 0 ? $totalAssessment / $totalExams : 0;
        $examsPaid = $perExam > 0 ? $totalPaid / $perExam : 0;
    @endphp
    
    <div class="card stat-card" style="border-left-color: var(--success-green);">
        <div class="stat-label">Exams Paid</div>
        <div class="stat-value">{{ number_format($examsPaid, 2) }}</div>
    </div>
    <div class="card stat-card" style="border-left-color: var(--success-green);">
        <div class="stat-label">Total Paid</div>
        <div class="stat-value">₱{{ number_format($totalPaid, 2) }}</div>
    </div>
    <div class="card stat-card error">
        <div class="stat-label">Current Balance</div>
        <div class="stat-value">₱{{ $payment ? number_format($payment->balance, 2) : '0.00' }}</div>
    </div>
    <div class="card stat-card warning">
        <div class="stat-label">Total Assessment</div>
        <div class="stat-value">₱{{ number_format($totalAssessment, 2) }}</div>
    </div>
    <div class="card stat-card info">
        <div class="stat-label">Per Exam</div>
        <div class="stat-value">₱{{ number_format($perExam, 2) }}</div>
    </div>
</div>

<!-- Announcements -->
@if($announcements->count() > 0)
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">📢 Announcements</h2>
    @foreach($announcements as $announcement)
        <div class="alert alert-info" style="margin-bottom: 12px;">
            <strong>{{ $announcement->title }}</strong><br>
            {{ $announcement->content }}
        </div>
    @endforeach
</div>
@endif

<!-- Enrolled Courses -->
<div class="table-container">
    <h2 class="card-title">Current Enrollment ({{ $totalUnits }} Units)</h2>
    
    @if($enrolledCourses->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Section</th>
                    <th>Units</th>
                    <th>Schedule</th>
                    <th>Room</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrolledCourses as $enrollment)
                    <tr>
                        <td><strong>{{ $enrollment->courseSection->course->course_code }}</strong></td>
                        <td>{{ $enrollment->courseSection->course->name }}</td>
                        <td>{{ $enrollment->courseSection->section_code }}</td>
                        <td>{{ $enrollment->courseSection->course->units }}</td>
                        <td>{{ $enrollment->courseSection->schedule ?? 'TBA' }}</td>
                        <td>{{ $enrollment->courseSection->room ?? 'TBA' }}</td>
                        <td>
                            @if($enrollment->status === 'enrolled')
                                <span class="badge badge-success">Paid</span>
                            @elseif($enrollment->status === 'pending_payment')
                                <span class="badge badge-warning">Pending Payment</span>
                            @else
                                <span class="badge badge-error">Dropped</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            <strong>No enrollments yet.</strong> Click "Enroll Course" to start enrolling in courses for this semester.
        </div>
    @endif
</div>

@if($payment && $payment->balance > 0)
<div style="margin-top: 20px; text-align: center;">
    <a href="{{ route('student.payments') }}" class="btn btn-primary">
        💳 Pay Now - Balance: ₱{{ number_format($payment->balance, 2) }}
    </a>
</div>
@endif
@endsection
