{{-- Admin Sidebar Navigation --}}
<!-- Dashboard (standalone) -->
<div class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" style="padding-left: 20px;"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>

<!-- Users Category -->
<div class="nav-category">
    <div class="category-header">
        <span>Users</span>
        <span class="category-icon">▼</span>
    </div>
    <div class="category-items">
        <div class="nav-item"><a href="{{ route('admin.students') }}" class="nav-link {{ request()->routeIs('admin.students') ? 'active' : '' }}"><span class="nav-icon">👥</span><span>Students</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.professors') }}" class="nav-link {{ request()->routeIs('admin.professors*') ? 'active' : '' }}"><span class="nav-icon">👨‍🏫</span><span>Professors</span></a></div>
    </div>
</div>

<!-- Academics Category -->
<div class="nav-category">
    <div class="category-header">
        <span>Academics</span>
        <span class="category-icon">▼</span>
    </div>
    <div class="category-items">
        <div class="nav-item"><a href="{{ route('admin.departments') }}" class="nav-link {{ request()->routeIs('admin.departments*') ? 'active' : '' }}"><span class="nav-icon">🏢</span><span>Departments</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.programs') }}" class="nav-link {{ request()->routeIs('admin.programs*') ? 'active' : '' }}"><span class="nav-icon">🎓</span><span>Programs</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.courses') }}" class="nav-link {{ request()->routeIs('admin.courses') ? 'active' : '' }}"><span class="nav-icon">📚</span><span>Courses</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.course-sections') }}" class="nav-link {{ request()->routeIs('admin.course-sections*') ? 'active' : '' }}"><span class="nav-icon">📝</span><span>Course Codes</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.semesters') }}" class="nav-link {{ request()->routeIs('admin.semesters*') ? 'active' : '' }}"><span class="nav-icon">📅</span><span>Semester</span></a></div>
    </div>
</div>

<!-- Utilities Category -->
<div class="nav-category">
    <div class="category-header">
        <span>Utilities</span>
        <span class="category-icon">▼</span>
    </div>
    <div class="category-items">
        <div class="nav-item"><a href="{{ route('admin.announcements') }}" class="nav-link {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}"><span class="nav-icon">📢</span><span>Announcement Management</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.enrollments') }}" class="nav-link {{ request()->routeIs('admin.enrollments*') ? 'active' : '' }}"><span class="nav-icon">✍️</span><span>Enrollment</span></a></div>
    </div>
</div>

<!-- Financial Category -->
<div class="nav-category">
    <div class="category-header">
        <span>Financial</span>
        <span class="category-icon">▼</span>
    </div>
    <div class="category-items">
        <div class="nav-item"><a href="{{ route('admin.payments') }}" class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"><span class="nav-icon">💳</span><span>Payment</span></a></div>
        <div class="nav-item"><a href="{{ route('admin.assessments') }}" class="nav-link {{ request()->routeIs('admin.assessments*') ? 'active' : '' }}"><span class="nav-icon">📋</span><span>Assessment</span></a></div>
    </div>
</div>
