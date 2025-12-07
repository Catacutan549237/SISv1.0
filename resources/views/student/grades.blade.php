@extends('layouts.dashboard')

@section('title', 'My Grades')

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
    <a href="{{ route('student.my-enrollment') }}" class="nav-link">
        <span class="nav-icon">📚</span>
        <span>Class Schedule</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.payments') }}" class="nav-link">
        <span class="nav-icon">💳</span>
        <span>Online Payment</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.grades') }}" class="nav-link active">
        <span class="nav-icon">🎓</span>
        <span>Evaluation</span>
    </a>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Academic Record</h1>
    <p class="page-subtitle">View your grades by semester</p>
</div>

@if($enrollments->count() > 0)
    @foreach($enrollments as $semesterName => $semesterEnrollments)
        <div class="table-container" style="margin-bottom: 30px;">
            <h2 class="card-title">{{ $semesterName }}</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Units</th>
                        <th>Grade</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalUnits = 0;
                        $totalGradePoints = 0;
                    @endphp
                    @foreach($semesterEnrollments as $enrollment)
                        @php
                            $units = $enrollment->courseSection->course->units;
                            $grade = $enrollment->grade ?? 0;
                            $totalUnits += $units;
                            $totalGradePoints += ($grade * $units);
                        @endphp
                        <tr>
                            <td><strong>{{ $enrollment->courseSection->course->course_code }}</strong></td>
                            <td>{{ $enrollment->courseSection->course->name }}</td>
                            <td>{{ $units }}</td>
                            <td><strong>{{ number_format($grade, 2) }}</strong></td>
                            <td>
                                @if($grade >= 75)
                                    <span class="badge badge-success">Passed</span>
                                @elseif($grade > 0)
                                    <span class="badge badge-error">Failed</span>
                                @else
                                    <span class="badge badge-warning">No Grade</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: var(--cream); font-weight: 600;">
                        <td colspan="2">Semester GPA</td>
                        <td>{{ $totalUnits }} units</td>
                        <td colspan="2">
                            @if($totalUnits > 0)
                                {{ number_format($totalGradePoints / $totalUnits, 2) }}
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
@else
    <div class="alert alert-info">
        No grades available yet. Grades will appear here once your professors have submitted them.
    </div>
@endif
@endsection
