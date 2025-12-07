@extends('layouts.dashboard')

@section('title', 'Enroll Course')

@section('sidebar')
<div class="nav-item"><a href="{{ route('student.dashboard') }}" class="nav-link"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>
<div class="nav-item"><a href="{{ route('student.enrollment') }}" class="nav-link active"><span class="nav-icon">📝</span><span>Enroll Course</span></a></div>
<div class="nav-item"><a href="{{ route('student.my-enrollment') }}" class="nav-link"><span class="nav-icon">📚</span><span>Class Schedule</span></a></div>
<div class="nav-item"><a href="{{ route('student.payments') }}" class="nav-link"><span class="nav-icon">💳</span><span>Online Payment</span></a></div>
<div class="nav-item"><a href="{{ route('student.grades') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Evaluation</span></a></div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Course Enrollment</h1>
    <p class="page-subtitle">{{ $currentSemester->name }}</p>
</div>

@if($program)
<div class="alert alert-info">
    <strong>Unit Requirements:</strong> You must enroll in a minimum of {{ $program->min_units }} units and a maximum of {{ $program->max_units }} units.<br>
    <strong>Current Units:</strong> {{ $currentUnits }} units enrolled
</div>
@endif

<form method="POST" action="{{ route('student.enrollment.store') }}" id="enrollmentForm">
    @csrf
    
    @foreach($availableSections as $category => $sections)
        <div class="table-container" style="margin-bottom: 30px;">
            <h2 class="card-title">{{ $category }}</h2>
            
            @php
                // Group sections by course
                $courseGroups = $sections->groupBy('course_id');
            @endphp
            
            @foreach($courseGroups as $courseId => $courseSections)
                @php
                    $course = $courseSections->first()->course;
                @endphp
                
                <!-- Course Header (Clickable) -->
                <div class="course-header" onclick="toggleCourse('course-{{ $courseId }}')" style="background: #f8f9fa; padding: 15px; margin-bottom: 5px; cursor: pointer; border-radius: 8px; border: 2px solid #e9ecef; transition: all 0.3s;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 16px; color: var(--sage-green);">{{ $course->course_code }}</strong>
                            <span style="margin-left: 15px; color: #666;">{{ $course->name }}</span>
                            <span class="badge badge-info" style="margin-left: 10px;">{{ $course->units }} units</span>
                            <span class="badge badge-secondary" style="margin-left: 5px;">{{ $courseSections->count() }} section(s)</span>
                        </div>
                        <div>
                            <span id="arrow-course-{{ $courseId }}" style="font-size: 20px; transition: transform 0.3s;">▼</span>
                        </div>
                    </div>
                </div>
                
                <!-- Course Sections (Hidden by default) -->
                <div id="course-{{ $courseId }}" class="course-sections" style="display: none; margin-bottom: 20px;">
                    <table style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Select</th>
                                <th>Section</th>
                                <th>Schedule</th>
                                <th>Room</th>
                                <th>Professor</th>
                                <th>Slots</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseSections as $section)
                                <tr>
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            name="course_sections[]" 
                                            value="{{ $section->id }}"
                                            class="form-checkbox course-checkbox"
                                            data-units="{{ $section->course->units }}"
                                            data-course-code="{{ $section->course->course_code }}"
                                        >
                                    </td>
                                    <td><strong>{{ $section->section_code }}</strong></td>
                                    <td>{{ $section->schedule ?? 'TBA' }}</td>
                                    <td>{{ $section->room ?? 'TBA' }}</td>
                                    <td>{{ $section->professor ? $section->professor->name : 'TBA' }}</td>
                                    <td>
                                        <span class="badge {{ $section->hasAvailableSlots() ? 'badge-success' : 'badge-error' }}">
                                            {{ $section->enrolled_count }}/{{ $section->max_students }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endforeach
    
    <!-- Unit Summary -->
    <div class="card" style="position: sticky; bottom: 20px; background: white; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong>Selected Units:</strong> <span id="selectedUnits">0</span> units<br>
                <strong>Total Units:</strong> <span id="totalUnits">{{ $currentUnits }}</span> units
                @if($program)
                    <span style="color: var(--gray-text); font-size: 14px;">(Min: {{ $program->min_units }}, Max: {{ $program->max_units }})</span>
                @endif
            </div>
            <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 12px 30px;">
                Enroll Selected Courses
            </button>
        </div>
        <div id="unitWarning" style="display: none; margin-top: 15px;"></div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const currentUnits = {{ $currentUnits }};
    const minUnits = {{ $program ? $program->min_units : 0 }};
    const maxUnits = {{ $program ? $program->max_units : 999 }};
    
    const checkboxes = document.querySelectorAll('.course-checkbox');
    const selectedUnitsEl = document.getElementById('selectedUnits');
    const totalUnitsEl = document.getElementById('totalUnits');
    const unitWarning = document.getElementById('unitWarning');
    const form = document.getElementById('enrollmentForm');
    
    // Track selected courses by course code
    const selectedCourses = new Map();
    
    function toggleCourse(courseId) {
        const courseDiv = document.getElementById(courseId);
        const arrow = document.getElementById('arrow-' + courseId);
        
        if (courseDiv.style.display === 'none') {
            courseDiv.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
        } else {
            courseDiv.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    }
    
    function updateUnits() {
        let selectedUnits = 0;
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedUnits += parseInt(checkbox.dataset.units);
            }
        });
        
        const totalUnits = currentUnits + selectedUnits;
        
        selectedUnitsEl.textContent = selectedUnits;
        totalUnitsEl.textContent = totalUnits;
        
        // Show warnings
        if (totalUnits < minUnits) {
            unitWarning.style.display = 'block';
            unitWarning.className = 'alert alert-warning';
            unitWarning.innerHTML = `<strong>Warning:</strong> Total units (${totalUnits}) is below the minimum required (${minUnits} units). Please select more courses.`;
            totalUnitsEl.style.color = 'var(--warning-yellow)';
        } else if (totalUnits > maxUnits) {
            unitWarning.style.display = 'block';
            unitWarning.className = 'alert alert-error';
            unitWarning.innerHTML = `<strong>Error:</strong> Total units (${totalUnits}) exceeds the maximum allowed (${maxUnits} units). Please remove some courses.`;
            totalUnitsEl.style.color = 'var(--error-red)';
        } else {
            unitWarning.style.display = 'none';
            totalUnitsEl.style.color = 'var(--success-green)';
        }
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            const courseCode = this.dataset.courseCode;
            
            if (this.checked) {
                // Check if this course is already selected
                if (selectedCourses.has(courseCode)) {
                    // Uncheck the previously selected section of the same course
                    const previousCheckbox = selectedCourses.get(courseCode);
                    previousCheckbox.checked = false;
                }
                // Store this checkbox as the selected one for this course
                selectedCourses.set(courseCode, this);
            } else {
                // Remove from selected courses if unchecked
                if (selectedCourses.get(courseCode) === this) {
                    selectedCourses.delete(courseCode);
                }
            }
            
            updateUnits();
        });
    });
    
    form.addEventListener('submit', function(e) {
        const totalUnits = currentUnits + parseInt(selectedUnitsEl.textContent);
        
        if (totalUnits < minUnits || totalUnits > maxUnits) {
            e.preventDefault();
            alert('Please ensure your total units are within the allowed range before submitting.');
        }
        
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one course to enroll.');
        }
    });
    
    // Add hover effect to course headers
    document.querySelectorAll('.course-header').forEach(header => {
        header.addEventListener('mouseenter', function() {
            this.style.background = '#e9ecef';
            this.style.borderColor = 'var(--sage-green)';
        });
        header.addEventListener('mouseleave', function() {
            this.style.background = '#f8f9fa';
            this.style.borderColor = '#e9ecef';
        });
    });
</script>
@endsection
