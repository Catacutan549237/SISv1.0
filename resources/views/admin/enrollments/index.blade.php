@extends('layouts.dashboard')

@section('title', 'Enrollments')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Enrollment Management</h1>
            <p class="page-subtitle">View and manage student enrollments</p>
        </div>
        <form action="{{ route('admin.enrollments') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search students or courses..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.enrollments') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.enrollments') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.enrollments', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
</div>

<div class="table-container">
    <h2 class="card-title">Student Enrollments ({{ $enrollments->total() }})</h2>
    
    @if($enrollments->count() > 0)
        @php
            // Group enrollments by student
            $groupedEnrollments = $enrollments->groupBy('student_id');
        @endphp
        
        @foreach($groupedEnrollments as $studentId => $studentEnrollments)
            @php
                $student = $studentEnrollments->first()->student;
                $enrollmentCount = $studentEnrollments->count();
            @endphp
            
            <!-- Student Header (Clickable) -->
            <div class="student-header" onclick="toggleStudent('student-{{ $studentId }}')" style="background: #f8f9fa; padding: 15px; margin-bottom: 5px; cursor: pointer; border-radius: 8px; border: 2px solid #e9ecef; transition: all 0.3s;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong style="font-size: 16px; color: var(--sage-green);">{{ $student->name }}</strong>
                        <span style="margin-left: 15px; color: #666;">{{ $student->student_id }}</span>
                        <span class="badge badge-info" style="margin-left: 10px;">{{ $enrollmentCount }} enrollment(s)</span>
                        @if($student->program)
                            <span class="badge badge-secondary" style="margin-left: 5px;">{{ $student->program->code }}</span>
                        @endif
                    </div>
                    <div>
                        <span id="arrow-student-{{ $studentId }}" style="font-size: 20px; transition: transform 0.3s;">▼</span>
                    </div>
                </div>
            </div>
            
            <!-- Student Enrollments (Hidden by default) -->
            <div id="student-{{ $studentId }}" class="student-enrollments" style="display: none; margin-bottom: 20px;">
                <table style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Section</th>
                            <th>Schedule</th>
                            <th>Professor</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentEnrollments as $enrollment)
                            <tr>
                                <td><strong>{{ $enrollment->courseSection->course->course_code }}</strong></td>
                                <td>{{ $enrollment->courseSection->course->name }}</td>
                                <td>{{ $enrollment->courseSection->section_code }}</td>
                                <td>{{ $enrollment->courseSection->schedule ?? 'TBA' }}</td>
                                <td>{{ $enrollment->courseSection->professor ? $enrollment->courseSection->professor->name : 'TBA' }}</td>
                                <td>{{ $enrollment->courseSection->semester->name }}</td>
                                <td>
                                    @if($enrollment->status === 'enrolled')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($enrollment->status === 'pending_payment')
                                        <span class="badge badge-warning">Pending Payment</span>
                                    @else
                                        <span class="badge badge-error">Dropped</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!request('archived'))
                                        <div style="display: flex; gap: 5px;">
                                            @if($enrollment->status !== 'dropped')
                                                <form method="POST" action="{{ route('admin.enrollments.drop', $enrollment) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Drop this course for {{ $student->name }}?')">Drop</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.enrollments.undrop', $enrollment) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Restore this course for {{ $student->name }}?')">Restore</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this enrollment?')">Archive</button>
                                            </form>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('admin.enrollments.restore', $enrollment->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Restore this enrollment record?')">Restore</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
        
        <div style="margin-top: 20px;">
            {{ $enrollments->links() }}
        </div>
    @else
        <div class="alert alert-info">No enrollments found.</div>
    @endif
</div>

<style>
.student-header:hover {
    background: #e9ecef !important;
    border-color: var(--sage-green) !important;
}
</style>

<script>
function toggleStudent(studentId) {
    const studentDiv = document.getElementById(studentId);
    const arrow = document.getElementById('arrow-' + studentId);
    
    if (studentDiv.style.display === 'none') {
        studentDiv.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        studentDiv.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection
