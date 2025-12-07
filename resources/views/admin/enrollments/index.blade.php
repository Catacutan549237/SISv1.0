@extends('layouts.dashboard')

@section('title', 'Enrollments')

@section('sidebar')
<div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>
<div class="nav-item"><a href="{{ route('admin.students') }}" class="nav-link"><span class="nav-icon">👥</span><span>Students</span></a></div>
<div class="nav-item"><a href="{{ route('admin.professors') }}" class="nav-link"><span class="nav-icon">👨‍🏫</span><span>Professors</span></a></div>
<div class="nav-item"><a href="{{ route('admin.departments') }}" class="nav-link"><span class="nav-icon">🏢</span><span>Departments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.programs') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Programs</span></a></div>
<div class="nav-item"><a href="{{ route('admin.courses') }}" class="nav-link"><span class="nav-icon">📚</span><span>Courses</span></a></div>
<div class="nav-item"><a href="{{ route('admin.course-sections') }}" class="nav-link"><span class="nav-icon">📝</span><span>Course Codes</span></a></div>
<div class="nav-item"><a href="{{ route('admin.semesters') }}" class="nav-link"><span class="nav-icon">📅</span><span>Semesters</span></a></div>
<div class="nav-item"><a href="{{ route('admin.enrollments') }}" class="nav-link active"><span class="nav-icon">✍️</span><span>Enrollments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.payments') }}" class="nav-link"><span class="nav-icon">💳</span><span>Payments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.announcements') }}" class="nav-link"><span class="nav-icon">📢</span><span>Announcements</span></a></div>
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Enrollment Management</h1>
            <p class="page-subtitle">Manually enroll students</p>
        </div>
        <form action="{{ route('admin.enrollments') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search enrollments..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.enrollments') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Manual Enrollment</h2>
    <form method="POST" action="{{ route('admin.enrollments.store') }}">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Student</label>
                <select name="student_id" class="form-input" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->student_id }} - {{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Course Section</label>
                <select name="course_section_id" class="form-input" required>
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->course->course_code }}({{ $section->section_code }}) - {{ $section->semester->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-input" required>
                    <option value="pending_payment">Pending Payment</option>
                    <option value="enrolled">Enrolled</option>
                    <option value="dropped">Dropped</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Enroll Student</button>
    </form>
</div>

<div class="table-container">
    <h2 class="card-title">All Enrollments ({{ $enrollments->total() }})</h2>
    @if($enrollments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Grade</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->student->student_id }} - {{ $enrollment->student->name }}</td>
                        <td><strong>{{ $enrollment->courseSection->course->course_code }}</strong></td>
                        <td>{{ $enrollment->courseSection->section_code }}</td>
                        <td>{{ $enrollment->courseSection->semester->name }}</td>
                        <td>
                            @if($enrollment->status === 'enrolled')
                                <span class="badge badge-success">Enrolled</span>
                            @elseif($enrollment->status === 'pending_payment')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-error">Dropped</span>
                            @endif
                        </td>
                        <td>{{ $enrollment->grade ? number_format($enrollment->grade, 2) : 'N/A' }}</td>
                        <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $enrollments->links() }}</div>
    @else
        <div class="alert alert-info">No enrollments found.</div>
    @endif
</div>
@endsection

