@extends('layouts.dashboard')

@section('title', 'Announcements')

@section('sidebar')
<div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>
<div class="nav-item"><a href="{{ route('admin.students') }}" class="nav-link"><span class="nav-icon">👥</span><span>Students</span></a></div>
<div class="nav-item"><a href="{{ route('admin.professors') }}" class="nav-link"><span class="nav-icon">👨‍🏫</span><span>Professors</span></a></div>
<div class="nav-item"><a href="{{ route('admin.departments') }}" class="nav-link"><span class="nav-icon">🏢</span><span>Departments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.programs') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Programs</span></a></div>
<div class="nav-item"><a href="{{ route('admin.courses') }}" class="nav-link"><span class="nav-icon">📚</span><span>Courses</span></a></div>
<div class="nav-item"><a href="{{ route('admin.course-sections') }}" class="nav-link"><span class="nav-icon">📝</span><span>Course Codes</span></a></div>
<div class="nav-item"><a href="{{ route('admin.semesters') }}" class="nav-link"><span class="nav-icon">📅</span><span>Semesters</span></a></div>
<div class="nav-item"><a href="{{ route('admin.enrollments') }}" class="nav-link"><span class="nav-icon">✍️</span><span>Enrollments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.payments') }}" class="nav-link"><span class="nav-icon">💳</span><span>Payments</span></a></div>
<div class="nav-item"><a href="{{ route('admin.announcements') }}" class="nav-link active"><span class="nav-icon">📢</span><span>Announcements</span></a></div>
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Announcement Management</h1>
            <p class="page-subtitle">Create and manage announcements</p>
        </div>
        <form action="{{ route('admin.announcements') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search announcements..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.announcements') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Create New Announcement</h2>
    <form method="POST" action="{{ route('admin.announcements.store') }}">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Target Audience</label>
                <select name="target_audience" class="form-input" required>
                    <option value="all">All Users</option>
                    <option value="students">Students Only</option>
                    <option value="professors">Professors Only</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-input" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" value="1" checked class="form-checkbox">
                <span>Active</span>
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Create Announcement</button>
    </form>
</div>

<div class="table-container">
    <h2 class="card-title">All Announcements ({{ $announcements->count() }})</h2>
    @if($announcements->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Audience</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($announcements as $announcement)
                    <tr>
                        <td><strong>{{ $announcement->title }}</strong></td>
                        <td>{{ Str::limit($announcement->content, 100) }}</td>
                        <td>
                            @if($announcement->target_audience === 'all')
                                <span class="badge badge-info">All</span>
                            @elseif($announcement->target_audience === 'students')
                                <span class="badge badge-success">Students</span>
                            @else
                                <span class="badge badge-warning">Professors</span>
                            @endif
                        </td>
                        <td>
                            @if($announcement->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-error">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $announcement->created_at->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No announcements found.</div>
    @endif
</div>
@endsection

