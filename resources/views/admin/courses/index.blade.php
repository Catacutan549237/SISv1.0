@extends('layouts.dashboard')

@section('title', 'Courses')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 class="page-title">Course Management</h1>
            <p class="page-subtitle">Manage courses and link to programs</p>
        </div>
        <form action="{{ route('admin.courses') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search courses..." value="{{ $search ?? '' }}" style="width: 250px;">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.courses') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.courses') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.courses', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
</div>


@if ($errors->any())
    <div class="alert alert-danger" style="background: #fde8e8; color: #c53030; border: 1px solid #c53030; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(!request('archived'))
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Add New Course</h2>
    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label class="form-label">Course Code</label>
                <input type="text" name="course_code" class="form-input" required placeholder="e.g., IT101">
            </div>
            <div class="form-group">
                <label class="form-label">Units</label>
                <input type="number" name="units" class="form-input" required value="3" min="1">
            </div>
            <div class="form-group">
                <label class="form-label">Course Type</label>
                <select name="is_general" class="form-input">
                    <option value="0">Major Subject</option>
                    <option value="1">General Education</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Course Name</label>
            <input type="text" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label">Link to Programs (for Major Subjects)</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @foreach($allPrograms as $program)
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="programs[]" value="{{ $program->id }}" class="form-checkbox">
                        <span>{{ $program->code }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-input" rows="2"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>
</div>
@endif

<div class="table-container">
    <h2 class="card-title">All Courses</h2>
    
    @if($programsWithCourses->isEmpty() && $uncategorizedCourses->isEmpty())
        <div class="alert alert-info">No courses found.</div>
    @else
        <!-- Program Groups -->
        @foreach($programsWithCourses as $program)
            @if($program->courses->isNotEmpty())
                <div onclick="toggleGroup('program-{{ $program->id }}')" class="course-header" style="background: #f8f9fa; padding: 15px; margin-bottom: 5px; cursor: pointer; border-radius: 8px; border: 2px solid #e9ecef; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong style="font-size: 16px; color: var(--sage-green);">{{ $program->code }}</strong>
                        <span style="margin-left: 15px; color: #666;">{{ $program->name }}</span>
                        <span class="badge badge-info" style="margin-left: 10px;">{{ $program->courses->count() }} courses</span>
                    </div>
                    <div>
                        <span id="arrow-program-{{ $program->id }}" style="font-size: 20px; transition: transform 0.3s;">▼</span>
                    </div>
                </div>

                <div id="program-{{ $program->id }}" style="display: none; margin-bottom: 20px; padding-left: 10px; padding-right: 10px;">
                    <table style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Units</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($program->courses as $course)
                                <tr>
                                    <td><strong>{{ $course->course_code }}</strong></td>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ $course->units }}</td>
                                    <td>
                                        @if($course->is_general)
                                            <span class="badge badge-info">General Ed</span>
                                        @else
                                            <span class="badge badge-success">Major</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(request('archived'))
                                            <form method="POST" action="{{ route('admin.courses.restore', $course->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive course: {{ $course->course_code }}?')">Archive</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        <!-- Uncategorized/General Courses -->
        @if($uncategorizedCourses->isNotEmpty())
            <div onclick="toggleGroup('uncategorized')" class="course-header" style="background: #f8f9fa; padding: 15px; margin-bottom: 5px; cursor: pointer; border-radius: 8px; border: 2px solid #e9ecef; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="font-size: 16px; color: #666;">Other / Uncategorized</strong>
                    <span class="badge badge-warning" style="margin-left: 10px;">{{ $uncategorizedCourses->count() }} courses</span>
                </div>
                <div>
                    <span id="arrow-uncategorized" style="font-size: 20px; transition: transform 0.3s;">▼</span>
                </div>
            </div>

            <div id="uncategorized" style="display: none; margin-bottom: 20px; padding-left: 10px; padding-right: 10px;">
                <table style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Units</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uncategorizedCourses as $course)
                            <tr>
                                <td><strong>{{ $course->course_code }}</strong></td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->units }}</td>
                                <td>
                                    @if($course->is_general)
                                        <span class="badge badge-info">General Ed</span>
                                    @else
                                        <span class="badge badge-success">Major</span>
                                    @endif
                                </td>
                                <td>
                                    @if(request('archived'))
                                        <form method="POST" action="{{ route('admin.courses.restore', $course->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive course: {{ $course->course_code }}?')">Archive</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
    function toggleGroup(id) {
        const content = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
            
            // Highlight header
            arrow.closest('.course-header').style.borderColor = 'var(--sage-green)';
            arrow.closest('.course-header').style.background = '#e9ecef';
        } else {
            content.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
            
            // Reset header style
            arrow.closest('.course-header').style.borderColor = '#e9ecef';
            arrow.closest('.course-header').style.background = '#f8f9fa';
        }
    }
</script>
@endsection
