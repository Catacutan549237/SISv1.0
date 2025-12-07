@extends('layouts.dashboard')

@section('title', 'My Courses')

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
            <h1 class="page-title">My Courses</h1>
            <p class="page-subtitle">{{ $currentSemester ? $currentSemester->name : 'No Active Semester' }}</p>
        </div>
        <form action="{{ route('professor.courses') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search courses..." value="{{ $search ?? '' }}" style="width: 250px;">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('professor.courses') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

@if($sections->count() > 0)
    @foreach($sections as $section)
        <div class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div>
                    <h2 class="card-title" style="margin-bottom: 8px;">
                        {{ $section->course->course_code }} ({{ $section->section_code }})
                    </h2>
                    <p style="color: var(--gray-text); margin-bottom: 4px;">{{ $section->course->name }}</p>
                    <p style="color: var(--gray-text); font-size: 14px;">
                        <strong>Schedule:</strong> {{ $section->schedule ?? 'TBA' }} | 
                        <strong>Room:</strong> {{ $section->room ?? 'TBA' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('professor.grades', $section) }}" class="btn btn-primary">
                        Manage Grades
                    </a>
                </div>
            </div>
            
            <div style="background: var(--cream); padding: 16px; border-radius: 8px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <div>
                        <div style="font-size: 13px; color: var(--gray-text); margin-bottom: 4px;">Units</div>
                        <div style="font-size: 20px; font-weight: 600;">{{ $section->course->units }}</div>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: var(--gray-text); margin-bottom: 4px;">Enrolled Students</div>
                        <div style="font-size: 20px; font-weight: 600;">{{ $section->enrolled_count }}</div>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: var(--gray-text); margin-bottom: 4px;">Max Capacity</div>
                        <div style="font-size: 20px; font-weight: 600;">{{ $section->max_students }}</div>
                    </div>
                </div>
            </div>
            
            @if($section->enrollments->count() > 0)
                <div style="margin-top: 16px;">
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Enrolled Students</h3>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 8px; background: var(--cream);">Student ID</th>
                                <th style="text-align: left; padding: 8px; background: var(--cream);">Name</th>
                                <th style="text-align: left; padding: 8px; background: var(--cream);">Status</th>
                                <th style="text-align: left; padding: 8px; background: var(--cream);">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section->enrollments as $enrollment)
                                <tr>
                                    <td style="padding: 8px;">{{ $enrollment->student->student_id }}</td>
                                    <td style="padding: 8px;">{{ $enrollment->student->name }}</td>
                                    <td style="padding: 8px;">
                                        @if($enrollment->status === 'enrolled')
                                            <span class="badge badge-success">Enrolled</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td style="padding: 8px;">
                                        {{ $enrollment->grade ? number_format($enrollment->grade, 2) : 'No grade' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
@else
    <div class="alert alert-info">
        @if(!empty($search))
            No courses found matching "{{ $search }}".
        @else
            No courses assigned for this semester.
        @endif
    </div>
@endif
@endsection
