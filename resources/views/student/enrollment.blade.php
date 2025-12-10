@extends('layouts.dashboard')

@section('title', 'Enroll Course')

@section('sidebar')
<div class="nav-item"><a href="{{ route('student.dashboard') }}" class="nav-link"><span class="nav-icon">📊</span><span>Dashboard</span></a></div>
<div class="nav-item"><a href="{{ route('student.enrollment') }}" class="nav-link active"><span class="nav-icon">📝</span><span>Enroll Course</span></a></div>
<div class="nav-item"><a href="{{ route('student.my-enrollment') }}" class="nav-link"><span class="nav-icon">📚</span><span>Class Schedule</span></a></div>
<div class="nav-item"><a href="{{ route('student.assessments') }}" class="nav-link"><span class="nav-icon">📋</span><span>Assessment</span></a></div>
<div class="nav-item"><a href="{{ route('student.payments') }}" class="nav-link"><span class="nav-icon">💳</span><span>Online Payment</span></a></div>
<div class="nav-item"><a href="{{ route('student.grades') }}" class="nav-link"><span class="nav-icon">🎓</span><span>Evaluation</span></a></div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Course Enrollment</h1>
    <p class="page-subtitle">{{ $currentSemester->name }}</p>
</div>

@if($program)
@endif

<form method="POST" action="{{ route('student.enrollment.store') }}" id="enrollmentForm">
    @csrf
    
    <!-- Two Panel Layout -->
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 20px; align-items: start;">
        
        <!-- LEFT PANEL: Available Courses -->
        <div>
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
                                        <tr class="section-row" data-section-id="{{ $section->id }}">
                                            <td>
                                                <input 
                                                    type="checkbox" 
                                                    name="course_sections[]" 
                                                    value="{{ $section->id }}"
                                                    class="form-checkbox course-checkbox"
                                                    data-section-id="{{ $section->id }}"
                                                    data-units="{{ $section->course->units }}"
                                                    data-course-code="{{ $section->course->course_code }}"
                                                    data-course-name="{{ $section->course->name }}"
                                                    data-section-code="{{ $section->section_code }}"
                                                    data-schedule="{{ $section->schedule ?? 'TBA' }}"
                                                    data-room="{{ $section->room ?? 'TBA' }}"
                                                    data-professor="{{ $section->professor ? $section->professor->name : 'TBA' }}"
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
        </div>
        
        <!-- RIGHT PANEL: Selected Courses (Sticky) -->
        <div style="position: sticky; top: 20px;">
            <div class="card" style="background: #f7fafc;">
                <h2 class="card-title" style="margin-bottom: 15px;">Selected Courses</h2>
                
                <!-- Unit Summary -->
                <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 2px solid #e9ecef;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Selected Units:</strong>
                        <span id="selectedUnits" style="font-weight: bold; color: var(--sage-green);">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Already Enrolled:</strong>
                        <span>{{ $currentUnits }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 2px solid #e9ecef;">
                        <strong>Total Units:</strong>
                        <span id="totalUnits" style="font-weight: bold; font-size: 18px;">{{ $currentUnits }}</span>
                    </div>
                    @if($program)
                        <div style="text-align: center; margin-top: 8px; color: var(--gray-text); font-size: 14px;">
                            (Min: {{ $program->min_units }}, Max: {{ $program->max_units }})
                        </div>
                    @endif
                </div>
                
                <!-- Conflict Warning -->
                <div id="conflictWarning" style="display: none; margin-bottom: 15px;"></div>
                
                <!-- Unit Warning -->
                <div id="unitWarning" style="display: none; margin-bottom: 15px;"></div>
                
                <!-- Selected Courses List -->
                <div id="selectedCoursesList" style="max-height: 400px; overflow-y: auto; margin-bottom: 15px;">
                    <div id="emptyState" style="text-align: center; padding: 40px 20px; color: #999;">
                        <p>No courses selected yet</p>
                        <p style="font-size: 14px;">Select courses from the left panel</p>
                    </div>
                </div>
                
                <!-- Important Reminder -->
                <div id="enrollmentReminder" style="display: none; background: #fff3cd; border: 2px solid #ffc107; border-radius: 6px; padding: 12px; margin-bottom: 15px;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 5px;">Important Reminder</div>
                    <div style="font-size: 13px; color: #856404; line-height: 1.5;">
                        Please review your selected courses carefully. Once submitted, you can no longer change courses.
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="btn btn-primary" style="width: 100%; font-size: 16px; padding: 12px;" disabled>
                    Enroll Selected Courses
                </button>
            </div>
        </div>
    </div>
</form>

<style>
.selected-course-item {
    background: white;
    padding: 8px 10px;
    border-radius: 6px;
    margin-bottom: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s;
}

.selected-course-item.has-conflict {
    border-color: #f56565;
    background: #fff5f5;
}

.selected-course-item .course-header-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
}

.selected-course-item .course-code {
    font-weight: bold;
    color: var(--sage-green);
    font-size: 13px;
    line-height: 1.3;
}

.selected-course-item .course-name {
    font-size: 12px;
    color: #666;
    margin: 2px 0;
    line-height: 1.3;
}

.selected-course-item .course-schedule {
    font-size: 12px;
    color: #555;
    margin-top: 3px;
}

.selected-course-item .remove-btn {
    background: #f56565;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 3px 7px;
    cursor: pointer;
    font-size: 11px;
    transition: background 0.3s;
    flex-shrink: 0;
    line-height: 1;
}

.selected-course-item .remove-btn:hover {
    background: #c53030;
}

.conflict-badge {
    background: #f56565;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: bold;
    display: inline-block;
    margin-top: 2px;
}
</style>
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
    const conflictWarning = document.getElementById('conflictWarning');
    const form = document.getElementById('enrollmentForm');
    const selectedCoursesList = document.getElementById('selectedCoursesList');
    const emptyState = document.getElementById('emptyState');
    const submitBtn = document.getElementById('submitBtn');
    
    // Track selected courses by course code
    const selectedCourses = new Map();
    const selectedSections = new Map();
    
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
    
    // Parse schedule string to get day and time ranges
    // Format: "130A-330A M-F" or "900M-1000M MWF"
    // Time format: HHMM[M|A|E] where M=Morning, A=Afternoon, E=Evening
    function parseSchedule(scheduleStr) {
        if (!scheduleStr || scheduleStr === 'TBA') return null;
        
        // Pattern: HHMM[M|A|E]-HHMM[M|A|E] DAYS
        const pattern = /(\d+[MAE])-(\d+[MAE])\s+(.+)/;
        const matches = scheduleStr.trim().match(pattern);
        
        if (!matches) return null;
        
        const startTime = parseTime(matches[1]);
        const endTime = parseTime(matches[2]);
        const days = parseDays(matches[3]);
        
        if (startTime === null || endTime === null || days.length === 0) {
            return null;
        }
        
        return { start: startTime, end: endTime, days: days };
    }
    
    // Parse time string to minutes since midnight
    // Format: HHMM[M|A|E]
    // Examples: 900M = 9:00 AM = 540 minutes, 130A = 1:30 PM = 810 minutes
    function parseTime(timeStr) {
        const pattern = /^(\d+)([MAE])$/;
        const matches = timeStr.match(pattern);
        
        if (!matches) return null;
        
        const time = matches[1];
        const period = matches[2];
        
        // Extract hours and minutes
        let hours = parseInt(time.slice(0, -2));
        const minutes = parseInt(time.slice(-2));
        
        // Adjust based on period
        if (period === 'A') {
            // Afternoon: 12pm - 5pm
            if (hours < 12) hours += 12;
        } else if (period === 'E') {
            // Evening: 5pm onwards
            if (hours < 12) hours += 12;
        }
        // M (Morning) stays as is (AM hours)
        
        return (hours * 60) + minutes;
    }
    
    // Parse days string into array of day codes
    // Examples: "M-F" = ['M', 'T', 'W', 'TH', 'F'], "MWF" = ['M', 'W', 'F'], "TTH" = ['T', 'TH']
    function parseDays(daysStr) {
        daysStr = daysStr.toUpperCase().trim();
        
        // Handle M-F (Monday to Friday)
        if (daysStr === 'M-F') {
            return ['M', 'T', 'W', 'TH', 'F'];
        }
        
        // Handle individual days
        const days = [];
        let i = 0;
        const len = daysStr.length;
        
        while (i < len) {
            // Check for TH (Thursday)
            if (i + 1 < len && daysStr.substr(i, 2) === 'TH') {
                days.push('TH');
                i += 2;
            } else {
                days.push(daysStr[i]);
                i++;
            }
        }
        
        return days;
    }
    
    // Check if two schedules conflict
    function hasScheduleConflict(schedule1, schedule2) {
        const s1 = parseSchedule(schedule1);
        const s2 = parseSchedule(schedule2);
        
        // If either schedule is null/TBA, no conflict
        if (!s1 || !s2) return false;
        
        // Check if they share any common days
        const commonDays = s1.days.filter(day => s2.days.includes(day));
        
        if (commonDays.length === 0) {
            return false; // No common days, no conflict
        }
        
        // Check if time ranges overlap
        // Conflict if: start1 < end2 AND start2 < end1
        const timeOverlap = (s1.start < s2.end) && (s2.start < s1.end);
        
        return timeOverlap;
    }
    
    // Detect conflicts among selected sections
    function detectConflicts() {
        const conflicts = new Map();
        const sections = Array.from(selectedSections.values());
        
        for (let i = 0; i < sections.length; i++) {
            for (let j = i + 1; j < sections.length; j++) {
                if (hasScheduleConflict(sections[i].schedule, sections[j].schedule)) {
                    if (!conflicts.has(sections[i].id)) conflicts.set(sections[i].id, []);
                    if (!conflicts.has(sections[j].id)) conflicts.set(sections[j].id, []);
                    
                    conflicts.get(sections[i].id).push(sections[j]);
                    conflicts.get(sections[j].id).push(sections[i]);
                }
            }
        }
        
        return conflicts;
    }
    
    // Update the selected courses display
    function updateSelectedCoursesDisplay() {
        if (selectedSections.size === 0) {
            emptyState.style.display = 'block';
            selectedCoursesList.innerHTML = '';
            selectedCoursesList.appendChild(emptyState);
            conflictWarning.style.display = 'none';
            return;
        }
        
        emptyState.style.display = 'none';
        const conflicts = detectConflicts();
        
        let html = '';
        selectedSections.forEach((section, sectionId) => {
            const hasConflict = conflicts.has(sectionId);
            const conflictClass = hasConflict ? 'has-conflict' : '';
            
            html += `
                <div class="selected-course-item ${conflictClass}">
                    <div class="course-header-info">
                        <div style="flex: 1; min-width: 0;">
                            <div class="course-code">${section.courseCode} - ${section.sectionCode} (${section.units} units)</div>
                            <div class="course-name">${section.courseName}</div>
                            <div class="course-schedule">${section.schedule}</div>
                            ${hasConflict ? '<span class="conflict-badge">CONFLICT</span>' : ''}
                        </div>
                        <button type="button" class="remove-btn" onclick="removeSection('${sectionId}')">✕</button>
                    </div>
                </div>
            `;
        });
        
        selectedCoursesList.innerHTML = html;
        
        // Show conflict warning if any - REAL TIME
        if (conflicts.size > 0) {
            conflictWarning.style.display = 'block';
            conflictWarning.className = 'alert alert-error';
            conflictWarning.innerHTML = '<strong>Schedule Conflict!</strong><br>Please adjust your selection.';
        } else {
            conflictWarning.style.display = 'none';
        }
    }
    
    // Remove a section from selection
    function removeSection(sectionId) {
        const checkbox = document.querySelector(`input[data-section-id="${sectionId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        }
    }
    
    // Update units and warnings
    function updateUnits() {
        let selectedUnits = 0;
        selectedSections.forEach(section => {
            selectedUnits += parseInt(section.units);
        });
        
        const totalUnits = currentUnits + selectedUnits;
        
        selectedUnitsEl.textContent = selectedUnits;
        totalUnitsEl.textContent = totalUnits;
        
        // Show warnings
        const conflicts = detectConflicts();
        const hasConflicts = conflicts.size > 0;
        
        if (totalUnits < minUnits) {
            unitWarning.style.display = 'block';
            unitWarning.className = 'alert alert-warning';
            unitWarning.innerHTML = `<strong>Warning:</strong> Need ${minUnits - totalUnits} more units.`;
            totalUnitsEl.style.color = 'var(--warning-yellow)';
            submitBtn.disabled = true;
        } else if (totalUnits > maxUnits) {
            unitWarning.style.display = 'block';
            unitWarning.className = 'alert alert-error';
            unitWarning.innerHTML = `<strong>Error:</strong> ${totalUnits - maxUnits} units over limit.`;
            totalUnitsEl.style.color = 'var(--error-red)';
            submitBtn.disabled = true;
        } else if (hasConflicts) {
            unitWarning.style.display = 'none';
            totalUnitsEl.style.color = 'var(--success-green)';
            submitBtn.disabled = true;
        } else if (selectedSections.size === 0) {
            unitWarning.style.display = 'none';
            totalUnitsEl.style.color = 'var(--sage-green)';
            submitBtn.disabled = true;
        } else {
            unitWarning.style.display = 'none';
            totalUnitsEl.style.color = 'var(--success-green)';
            submitBtn.disabled = false;
        }
        
        // Show/hide enrollment reminder
        const enrollmentReminder = document.getElementById('enrollmentReminder');
        if (selectedSections.size > 0 && !hasConflicts && totalUnits >= minUnits && totalUnits <= maxUnits) {
            enrollmentReminder.style.display = 'block';
        } else {
            enrollmentReminder.style.display = 'none';
        }
        
        updateSelectedCoursesDisplay();
    }
    
    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const sectionId = this.dataset.sectionId;
            const courseCode = this.dataset.courseCode;
            
            if (this.checked) {
                // Check if this course is already selected
                if (selectedCourses.has(courseCode)) {
                    // Uncheck the previously selected section of the same course
                    const previousCheckbox = selectedCourses.get(courseCode);
                    previousCheckbox.checked = false;
                    selectedSections.delete(previousCheckbox.dataset.sectionId);
                }
                
                // Store this checkbox as the selected one for this course
                selectedCourses.set(courseCode, this);
                selectedSections.set(sectionId, {
                    id: sectionId,
                    courseCode: this.dataset.courseCode,
                    courseName: this.dataset.courseName,
                    sectionCode: this.dataset.sectionCode,
                    schedule: this.dataset.schedule,
                    units: this.dataset.units
                });
            } else {
                // Remove from selected courses if unchecked
                if (selectedCourses.get(courseCode) === this) {
                    selectedCourses.delete(courseCode);
                }
                selectedSections.delete(sectionId);
            }
            
            updateUnits();
        });
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const totalUnits = currentUnits + parseInt(selectedUnitsEl.textContent);
        const conflicts = detectConflicts();
        
        if (conflicts.size > 0) {
            e.preventDefault();
            alert('You have schedule conflicts! Please resolve them before submitting.');
            return;
        }
        
        if (totalUnits < minUnits || totalUnits > maxUnits) {
            e.preventDefault();
            alert('Please ensure your total units are within the allowed range before submitting.');
            return;
        }
        
        if (selectedSections.size === 0) {
            e.preventDefault();
            alert('Please select at least one course to enroll.');
            return;
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
    
    // Make removeSection globally accessible
    window.removeSection = removeSection;
</script>
@endsection
