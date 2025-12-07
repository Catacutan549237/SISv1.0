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
        <h2 class="card-title">Student Grades</h2>
        
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
                                <input 
                                    type="number" 
                                    name="grades[{{ $enrollment->id }}]" 
                                    class="form-input" 
                                    value="{{ $enrollment->grade }}"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="Enter grade (0-100)"
                                    style="max-width: 150px;"
                                >
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
