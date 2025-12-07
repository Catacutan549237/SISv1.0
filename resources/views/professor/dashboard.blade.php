@extends('layouts.dashboard')

@section('title', 'Professor Dashboard')

@section('sidebar')
<div class="nav-item">
    <a href="{{ route('professor.dashboard') }}" class="nav-link active">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('professor.courses') }}" class="nav-link">
        <span class="nav-icon">📚</span>
        <span>My Courses</span>
    </a>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Professor Dashboard</h1>
    <p class="page-subtitle">{{ $currentSemester ? $currentSemester->name : 'No Active Semester' }}</p>
</div>

<!-- Stats -->
<div class="card-grid">
    <div class="card stat-card">
        <div class="stat-label">Assigned Sections</div>
        <div class="stat-value">{{ $assignedSections->count() }}</div>
    </div>
    <div class="card stat-card info">
        <div class="stat-label">Total Students</div>
        <div class="stat-value">{{ $totalStudents }}</div>
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

<!-- Assigned Courses -->
<div class="table-container">
    <h2 class="card-title">My Assigned Courses</h2>
    
    @if($assignedSections->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Section</th>
                    <th>Schedule</th>
                    <th>Room</th>
                    <th>Enrolled</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignedSections as $section)
                    <tr>
                        <td><strong>{{ $section->course->course_code }}</strong></td>
                        <td>{{ $section->course->name }}</td>
                        <td>{{ $section->section_code }}</td>
                        <td>{{ $section->schedule ?? 'TBA' }}</td>
                        <td>{{ $section->room ?? 'TBA' }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ $section->enrolled_count }}/{{ $section->max_students }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('professor.grades', $section) }}" class="btn btn-primary btn-sm">
                                Manage Grades
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            No courses assigned for this semester.
        </div>
    @endif
</div>
@endsection
