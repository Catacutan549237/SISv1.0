@extends('layouts.dashboard')

@section('title', 'Students')

@section('sidebar')
<div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>
<div class="nav-item"><a href="{{ route('admin.students') }}" class="nav-link active"><span class="nav-icon">👥</span><span>Students</span></a></div>
<div class="nav-item"><a href="{{ route('admin.professors') }}" class="nav-link"><span class="nav-icon">👨‍🏫</span><span>Professors</span></a></div>
<div class="nav-item"><a href="{{ route('admin.departments') }}" class="nav-link"><span class="nav-icon">🏢</span><span>Departments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.programs') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Programs</span></a></div>
<div class="nav-item"><a href="{{ route('admin.courses') }}" class="nav-link"><span class="nav-icon">📚</span><span>Courses</span></a></div>
<div class="nav-item"><a href="{{ route('admin.course-sections') }}" class="nav-link"><span class="nav-icon">📝</span><span>Course Codes</span></a></div>
<div class="nav-item"><a href="{{ route('admin.semesters') }}" class="nav-link"><span class="nav-icon">📅</span><span>Semesters</span></a></div>
<div class="nav-item"><a href="{{ route('admin.enrollments') }}" class="nav-link"><span class="nav-icon">✍️</span><span>Enrollments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.payments') }}" class="nav-link"><span class="nav-icon">💳</span><span>Payments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.announcements') }}" class="nav-link"><span class="nav-icon">📢</span><span>Announcements</span></a></div>
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Student Management</h1>
            <p class="page-subtitle">View and manage all students</p>
        </div>
        <form action="{{ route('admin.students') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search students..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.students') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="table-container">
    <h2 class="card-title">All Students ({{ $students->total() }})</h2>
    
    @if($students->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Year Level</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td><strong>{{ $student->student_id }}</strong></td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>
                            @if($student->program)
                                {{ $student->program->code }}
                            @else
                                <span class="badge badge-warning">No Program</span>
                            @endif
                        </td>
                        <td>{{ $student->year_level ?? 'N/A' }}</td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $students->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No students found.
        </div>
    @endif
</div>
@endsection
