@extends('layouts.dashboard')

@section('title', 'Course Codes')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 class="page-title">Course Section Management</h1>
            <p class="page-subtitle">Create sections and assign professors</p>
        </div>
        <form action="{{ route('admin.course-sections') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search sections..." value="{{ $search ?? '' }}" style="width: 250px;">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.course-sections') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.course-sections') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.course-sections', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
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
    <h2 class="card-title">Add New Course Section</h2>
    <form method="POST" action="{{ route('admin.course-sections.store') }}">
        @csrf
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group" style="position: relative;">
                <label class="form-label">Course</label>
                <input type="text" id="courseSearchInput" class="form-input" placeholder="Select Course..." autocomplete="off">
                <select name="course_id" id="courseSelect" class="form-input" required size="5" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; height: 150px; overflow-y: auto; background: white; border: 1px solid #ddd; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select name="semester_id" class="form-input" required>
                    <option value="">Select Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>{{ $semester->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Section Code</label>
                <input type="text" name="section_code" class="form-input" required placeholder="e.g., 0001">
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label class="form-label">Professor</label>
                <select name="professor_id" class="form-input">
                    <option value="">No Professor</option>
                    @foreach($professors as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Max Students</label>
                <input type="number" name="max_students" class="form-input" required value="40" min="1">
            </div>
            <div class="form-group">
                <label class="form-label">Schedule</label>
                <input type="text" name="schedule" class="form-input" placeholder="e.g., 900M-1000M M-F">
            </div>
            <div class="form-group">
                <label class="form-label">Room</label>
                <input type="text" name="room" class="form-input" placeholder="e.g., Room 301">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Section</button>
    </form>
</div>
@endif

<div class="table-container">
    <h2 class="card-title">All Course Codes</h2>
    
    @if($sections->isEmpty())
        <div class="alert alert-info">No Course Codes found.</div>
    @else
        @php
            $groupedSections = $sections->groupBy('course_id');
        @endphp
        
        @foreach($groupedSections as $courseId => $courseSections)
            @php
                $course = $courseSections->first()->course;
            @endphp
            
            <div onclick="toggleGroup('course-{{ $course->id }}')" class="course-header" style="background: #f8f9fa; padding: 15px; margin-bottom: 5px; cursor: pointer; border-radius: 8px; border: 2px solid #e9ecef; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center;">
                <div>
                     <strong style="font-size: 16px; color: var(--sage-green);">{{ $course->course_code }}</strong>
                     <span style="margin-left: 15px; color: #666;">{{ $course->name }}</span>
                     <span class="badge badge-info" style="margin-left: 10px;">{{ $courseSections->count() }} sections</span>
                </div>
                <div><span id="arrow-course-{{ $course->id }}" style="font-size: 20px; transition: transform 0.3s;">▼</span></div>
            </div>
            
            <div id="course-{{ $course->id }}" style="display: none; margin-bottom: 20px; padding-left: 10px; padding-right: 10px;">
                <table style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Semester</th>
                            <th>Professor</th>
                            <th>Schedule</th>
                            <th>Room</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courseSections as $section)
                            <tr>
                                <td><strong>{{ $section->section_code }}</strong></td>
                                <td>{{ $section->semester->name }}</td>
                                <td>{{ $section->professor ? $section->professor->name : 'TBA' }}</td>
                                <td>{{ $section->schedule ?? 'TBA' }}</td>
                                <td>{{ $section->room ?? 'TBA' }}</td>
                                <td><span class="badge badge-info">{{ $section->enrolled_count }}/{{ $section->max_students }}</span></td>
                                <td>

                                    @if(request('archived'))
                                        <form method="POST" action="{{ route('admin.course-sections.restore', $section->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @else
                                        <button onclick="editSection({{ $section->id }}, '{{ $section->course_id }}', '{{ $section->professor_id }}', '{{ $section->section_code }}', '{{ $section->max_students }}', '{{ $section->schedule }}', '{{ $section->room }}', {{ ($section->grades_visible ?? false) ? 'true' : 'false' }})" class="btn btn-primary btn-sm">Edit</button>
                                        <form method="POST" action="{{ route('admin.course-sections.destroy', $section) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this section?')">Archive</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 class="card-title">Edit Course Section</h2>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Professor</label>
                    <select id="edit_professor_id" name="professor_id" class="form-input">
                        <option value="">No Professor</option>
                        @foreach($professors as $prof)
                            <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Section Code</label>
                    <input type="text" id="edit_section_code" name="section_code" class="form-input" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Max Students</label>
                    <input type="number" id="edit_max_students" name="max_students" class="form-input" required min="1">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Schedule</label>
                    <input type="text" id="edit_schedule" name="schedule" class="form-input" placeholder="e.g., 900M-1000M M-F">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Room</label>
                    <input type="text" id="edit_room" name="room" class="form-input" placeholder="e.g., Room 301">
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 10px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" id="edit_grades_visible" name="grades_visible" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                    <span class="form-label" style="margin: 0;">Show Grades to Students</span>
                </label>
                <p style="font-size: 13px; color: #666; margin-top: 5px; margin-left: 30px;">Enable this to allow students to view their grades for this section</p>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Section</button>
            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
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
        arrow.closest('.course-header').style.borderColor = 'var(--sage-green)';
        arrow.closest('.course-header').style.background = '#e9ecef';
    } else {
        content.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
        arrow.closest('.course-header').style.borderColor = '#e9ecef';
        arrow.closest('.course-header').style.background = '#f8f9fa';
    }
}

function editSection(id, courseId, professorId, sectionCode, maxStudents, schedule, room, gradesVisible) {
    document.getElementById('editForm').action = '/admin/course-sections/' + id;
    document.getElementById('edit_professor_id').value = professorId || '';
    document.getElementById('edit_section_code').value = sectionCode;
    document.getElementById('edit_max_students').value = maxStudents;
    document.getElementById('edit_schedule').value = schedule || '';
    document.getElementById('edit_room').value = room || '';
    document.getElementById('edit_grades_visible').checked = gradesVisible;
    document.getElementById('editModal').style.display = 'flex';
}


function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Course Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('courseSearchInput');
    const select = document.getElementById('courseSelect');
    
    if (searchInput && select) {
        // Store original options
        const originalOptions = Array.from(select.options);
        
        // Show dropdown on focus
        searchInput.addEventListener('focus', function() {
            select.style.display = 'block';
        });

        // Filter and show on input
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            select.style.display = 'block';
            select.innerHTML = '';
            
            let hasMatch = false;
            originalOptions.forEach(function(option) {
                if (option.text.toLowerCase().includes(filter)) {
                    select.add(option);
                    hasMatch = true;
                }
            });
            
            if (!hasMatch) {
                const noResult = document.createElement('option');
                noResult.text = "No matching courses";
                noResult.disabled = true;
                select.add(noResult);
            }
        });

        // Handle selection (click on option)
        select.addEventListener('change', function() {
             selectOption();
        });
        
        select.addEventListener('click', function(e) {
            // Also handle direct clicks on options (sometimes 'change' doesn't fire if value doesn't change but we want to confirm selection UI)
            if (e.target.tagName === 'OPTION') {
                selectOption();
            }
        });

        function selectOption() {
            if (select.selectedIndex !== -1) {
                const selectedOption = select.options[select.selectedIndex];
                if (!selectedOption.disabled && selectedOption.value !== "") {
                    searchInput.value = selectedOption.text;
                    select.style.display = 'none';
                }
            }
        }

        // Hide when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !select.contains(e.target)) {
                select.style.display = 'none';
                
                // Optional: Validate if input matches a selected value?
                // For now, let's keep the last valid selection in the select element.
            }
        });
    }
});
</script>
@endsection
