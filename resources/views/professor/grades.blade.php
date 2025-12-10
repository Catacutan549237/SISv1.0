@extends('layouts.dashboard')

@section('title', 'Manage Grades')

@section('sidebar')
<div class="nav-item">
    <a href="{{ route('professor.dashboard') }}" class="nav-link">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('professor.courses') }}" class="nav-link active">
        <span class="nav-icon">📚</span>
        <span>My Courses</span>
    </a>
</div>
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 class="page-title">Manage Grades</h1>
            <p class="page-subtitle">{{ $section->course->course_code }} ({{ $section->section_code }}) - {{ $section->course->name }}</p>
        </div>
        <form action="{{ route('professor.grades', $section) }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search students..." value="{{ $search ?? '' }}" style="width: 250px;">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('professor.grades', $section) }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<form method="POST" action="{{ route('professor.grades.update', $section) }}">
    @csrf
    
    <div class="table-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 class="card-title" style="margin: 0;">Student Grades</h2>
            <div class="alert alert-info" style="margin: 0; padding: 10px 16px; font-size: 13px;">
                <strong>Grading Scale:</strong> 1.0 (Fail), 2.0 - 4.0 (Pass), 7.1 (No Permit), 7.2 (Incomplete)
            </div>
        </div>
        
        @if($enrollments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student->student_id }}</td>
                            <td>{{ $enrollment->student->name }}</td>
                            <td>
                                @if($enrollment->status === 'enrolled')
                                    <span class="badge badge-success">Enrolled</span>
                                @else
                                    <span class="badge badge-warning">Pending Payment</span>
                                @endif
                            </td>
                            <td>
                                <select 
                                    name="grades[{{ $enrollment->id }}]" 
                                    class="form-select" 
                                    style="max-width: 180px; padding: 8px;"
                                >
                                    <option value="" {{ $enrollment->grade === null ? 'selected' : '' }}>Select Grade</option>
                                    <option value="4.0" {{ $enrollment->grade == 4.0 ? 'selected' : '' }}>4.0 - High Distinction</option>
                                    <option value="3.5" {{ $enrollment->grade == 3.5 ? 'selected' : '' }}>3.5 - Distinction</option>
                                    <option value="3.0" {{ $enrollment->grade == 3.0 ? 'selected' : '' }}>3.0 - Very Good</option>
                                    <option value="2.5" {{ $enrollment->grade == 2.5 ? 'selected' : '' }}>2.5 - Good</option>
                                    <option value="2.0" {{ $enrollment->grade == 2.0 ? 'selected' : '' }}>2.0 - Average</option>
                                    <option value="1.0" {{ $enrollment->grade == 1.0 ? 'selected' : '' }}>1.0 - Fail</option>
                                    <option value="7.1" {{ $enrollment->grade == 7.1 ? 'selected' : '' }}>7.1 - No Permit</option>
                                    <option value="7.2" {{ $enrollment->grade == 7.2 ? 'selected' : '' }}>7.2 - Incomplete</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save Grades</button>
                <a href="{{ route('professor.courses') }}" class="btn btn-secondary">Cancel</a>
            </div>
        @else
            <div class="alert alert-info">
                @if(!empty($search))
                    No students found matching "{{ $search }}".
                @else
                    No students enrolled in this section yet.
                @endif
            </div>
        @endif
    </div>
</form>
@endsection
