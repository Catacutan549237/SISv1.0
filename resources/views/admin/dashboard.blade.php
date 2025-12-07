@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('sidebar')
<div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link active"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>
<div class="nav-item"><a href="{{ route('admin.students') }}" class="nav-link"><span class="nav-icon">👥</span><span>Students</span></a></div>
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
    <h1 class="page-title">Analytics Dashboard</h1>
    <p class="page-subtitle">System Overview and Performance Metrics</p>
</div>

<!-- Student Metrics -->
<div style="margin-bottom: 20px;">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--dark-text);">📚 Student Metrics</h3>
    <div class="card-grid">
        <div class="card stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ $totalStudents }}</div>
            <div style="font-size: 12px; color: var(--gray-text); margin-top: 8px;">All registered students</div>
        </div>
        <div class="card stat-card" style="border-left-color: var(--success-green);">
            <div class="stat-label">Active Students</div>
            <div class="stat-value">{{ $activeStudents }}</div>
            <div style="font-size: 12px; color: var(--gray-text); margin-top: 8px;">Currently enrolled</div>
        </div>
        <div class="card stat-card error">
            <div class="stat-label">Inactive Students</div>
            <div class="stat-value">{{ $inactiveStudents }}</div>
            <div style="font-size: 12px; color: var(--gray-text); margin-top: 8px;">Not enrolled this semester</div>
        </div>
        <div class="card stat-card" style="border-left-color: var(--success-green);">
            <div class="stat-label">Total Paid</div>
            <div class="stat-value" style="font-size: 28px;">₱{{ number_format($totalPaid, 2) }}</div>
            <div style="font-size: 12px; color: var(--gray-text); margin-top: 8px;">Total amount collected</div>
        </div>
    </div>
</div>

<!-- Academic Resources -->
<div style="margin-bottom: 20px;">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--dark-text);">🎓 Academic Resources</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div class="card stat-card warning">
            <div class="stat-label">Total Professors</div>
            <div class="stat-value">{{ $totalProfessors }}</div>
        </div>
        <div class="card stat-card">
            <div class="stat-label">Total Courses</div>
            <div class="stat-value">{{ $totalCourses }}</div>
        </div>
        <div class="card stat-card info">
            <div class="stat-label">Programs</div>
            <div class="stat-value">{{ $totalPrograms }}</div>
        </div>
        <div class="card stat-card" style="border-left-color: var(--success-green);">
            <div class="stat-label">Departments</div>
            <div class="stat-value">{{ $totalDepartments }}</div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <!-- Student Distribution Chart -->
    <div class="card">
        <h2 class="card-title">Student Status Distribution</h2>
        <canvas id="studentChart" style="max-height: 300px;"></canvas>
    </div>

    <!-- Academic Resources Chart -->
    <div class="card">
        <h2 class="card-title">Academic Resources Overview</h2>
        <canvas id="resourcesChart" style="max-height: 300px;"></canvas>
    </div>
</div>

<!-- Recent Activity -->
<div class="table-container">
    <h2 class="card-title">Recent Enrollments</h2>
    
    @if($recentEnrollments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentEnrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->student->name }}</td>
                        <td>{{ $enrollment->courseSection->course->course_code }} - {{ $enrollment->courseSection->course->name }}</td>
                        <td>
                            @if($enrollment->status === 'enrolled')
                                <span class="badge badge-success">Enrolled</span>
                            @elseif($enrollment->status === 'pending_payment')
                                <span class="badge badge-warning">Pending Payment</span>
                            @else
                                <span class="badge badge-error">Dropped</span>
                            @endif
                        </td>
                        <td>{{ $enrollment->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No enrollments yet.</div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Student Status Chart
    const studentCtx = document.getElementById('studentChart').getContext('2d');
    new Chart(studentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Students', 'Inactive Students'],
            datasets: [{
                data: [{{ $activeStudents }}, {{ $inactiveStudents }}],
                backgroundColor: [
                    'rgba(74, 124, 44, 0.8)',
                    'rgba(197, 48, 48, 0.8)'
                ],
                borderColor: [
                    'rgba(74, 124, 44, 1)',
                    'rgba(197, 48, 48, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Resources Chart
    const resourcesCtx = document.getElementById('resourcesChart').getContext('2d');
    new Chart(resourcesCtx, {
        type: 'bar',
        data: {
            labels: ['Professors', 'Courses', 'Programs', 'Departments'],
            datasets: [{
                label: 'Count',
                data: [{{ $totalProfessors }}, {{ $totalCourses }}, {{ $totalPrograms }}, {{ $totalDepartments }}],
                backgroundColor: [
                    'rgba(214, 158, 46, 0.8)',
                    'rgba(74, 124, 44, 0.8)',
                    'rgba(49, 130, 206, 0.8)',
                    'rgba(56, 161, 105, 0.8)'
                ],
                borderColor: [
                    'rgba(214, 158, 46, 1)',
                    'rgba(74, 124, 44, 1)',
                    'rgba(49, 130, 206, 1)',
                    'rgba(56, 161, 105, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection

