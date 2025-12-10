@extends('layouts.dashboard')

@section('title', 'My Enrollment')

@section('sidebar')
<div class="nav-item">
    <a href="{{ route('student.dashboard') }}" class="nav-link">
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
    <a href="{{ route('student.my-enrollment') }}" class="nav-link active">
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
    <h1 class="page-title">My Class Schedule</h1>
    <p class="page-subtitle">{{ $currentSemester ? $currentSemester->name : 'No Active Semester' }}</p>
</div>

<div class="alert alert-info">
    <strong>Total Units:</strong> {{ $totalUnits }} units
</div>

<div class="table-container">
    <h2 class="card-title">Enrolled Courses</h2>
    
    @if($enrollments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Section</th>
                    <th>Units</th>
                    <th>Schedule</th>
                    <th>Room</th>
                    <th>Professor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                    <tr>
                        <td><strong>{{ $enrollment->courseSection->course->course_code }}</strong></td>
                        <td>{{ $enrollment->courseSection->course->name }}</td>
                        <td>{{ $enrollment->courseSection->section_code }}</td>
                        <td>{{ $enrollment->courseSection->course->units }}</td>
                        <td>{{ $enrollment->courseSection->schedule ?? 'TBA' }}</td>
                        <td>{{ $enrollment->courseSection->room ?? 'TBA' }}</td>
                        <td>{{ $enrollment->courseSection->professor ? $enrollment->courseSection->professor->name : 'TBA' }}</td>
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
            No enrollments for this semester. <a href="{{ route('student.enrollment') }}">Enroll now</a>
        </div>
    @endif
</div>
@endsection
