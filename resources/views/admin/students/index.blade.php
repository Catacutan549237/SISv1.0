@extends('layouts.dashboard')

@section('title', 'Students')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection


@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Student Management</h1>
            <p class="page-subtitle">Manage student accounts</p>
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

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.students', ['status' => 'active']) }}" class="btn {{ request('status', 'active') == 'active' ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.students', ['status' => 'deactivated']) }}" class="btn {{ request('status') == 'deactivated' ? 'btn-primary' : 'btn-secondary' }}">Deactivated</a>
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

<!-- Add Student Form -->
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Add New Student</h2>
    
    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-input" required placeholder="e.g., John Doe">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required placeholder="e.g., student@university.com">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="program_id" class="form-label">Program</label>
                <select id="program_id" name="program_id" class="form-input">
                    <option value="">Select Program (Optional)</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }} ({{ $program->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="year_level" class="form-label">Year Level</label>
                <select id="year_level" name="year_level" class="form-input" required>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                </select>
            </div>
        </div>
        
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <strong>Note:</strong> A student ID and temporary password will be generated automatically. You can view them later in the Actions menu.
        </div>
        
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>
</div>

<!-- Students List -->
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
                    <th>Password Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
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
                        <td>
                            @if($student->must_change_password)
                                <span class="badge badge-warning">Pending Change</span>
                            @else
                                <span class="badge badge-success">Password Changed</span>
                            @endif
                        </td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                        <td>
                            <button onclick="editStudent({{ $student->id }}, '{{ $student->name }}', '{{ $student->email }}', '{{ $student->program_id }}', '{{ $student->year_level }}')" class="btn btn-primary btn-sm">Edit</button>
                            
                            @if($student->must_change_password && $student->temp_password)
                                <button onclick="showTempPassword('{{ $student->name }}', '{{ $student->student_id }}', '{{ $student->temp_password }}')" class="btn btn-info btn-sm">View Credentials</button>
                            @endif
                            
                            @if($student->is_active)
                                <button onclick="deactivateStudent({{ $student->id }}, '{{ $student->name }}')" class="btn btn-danger btn-sm">Deactivate</button>
                            @else
                                <form method="POST" action="{{ route('admin.students.activate', $student) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">{{ $students->links() }}</div>
    @else
        <div class="alert alert-info">No students found.</div>
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 class="card-title">Edit Student</h2>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="edit_name" class="form-label">Full Name</label>
                <input type="text" id="edit_name" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_email" class="form-label">Email Address</label>
                <input type="email" id="edit_email" name="email" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_program_id" class="form-label">Program</label>
                <select id="edit_program_id" name="program_id" class="form-input">
                    <option value="">Select Program (Optional)</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }} ({{ $program->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_year_level" class="form-label">Year Level</label>
                <select id="edit_year_level" name="year_level" class="form-input" required>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Student</button>
            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<!-- Temp Password Modal -->
<div id="tempPasswordModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; width: 90%;">
        <h2 class="card-title">Student Credentials</h2>
        
        <p><strong id="temp_student_name"></strong></p>
        
        <div class="alert alert-info" style="margin: 20px 0;">
            <strong>Student ID:</strong>
            <div style="font-size: 20px; font-weight: 700; margin-top: 10px; color: var(--sage-green);" id="temp_student_id"></div>
        </div>
        
        <div class="alert alert-info" style="margin: 20px 0;">
            <strong>Temporary Password:</strong>
            <div style="font-size: 24px; font-weight: 700; margin-top: 10px; color: var(--sage-green);" id="temp_password_display"></div>
        </div>
        
        <p style="color: var(--gray-text); font-size: 14px;">
            Share these credentials with the student. They will be required to change their password on first login.
        </p>
        
        <button type="button" onclick="closeTempPasswordModal()" class="btn btn-primary">Close</button>
    </div>
</div>

<!-- Deactivate Modal -->
<div id="deactivateModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; width: 90%;">
        <h2 class="card-title">Deactivate Student</h2>
        
        <p>Are you sure you want to deactivate <strong id="deactivate_student_name"></strong>?</p>
        
        <form id="deactivateForm" method="POST">
            @csrf
            
            <div class="form-group" style="margin-top: 15px;">
                <label for="deactivation_reason" class="form-label">Reason for Deactivation</label>
                <textarea id="deactivation_reason" name="deactivation_reason" class="form-input" rows="4" required placeholder="Please provide a reason..."></textarea>
            </div>
            
            <div style="margin-top: 20px; text-align: right;">
                <button type="button" onclick="closeDeactivateModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">Deactivate</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editStudent(id, name, email, programId, yearLevel) {
    document.getElementById('editForm').action = '/admin/students/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_program_id').value = programId || '';
    document.getElementById('edit_year_level').value = yearLevel;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function showTempPassword(name, studentId, password) {
    document.getElementById('temp_student_name').textContent = name;
    document.getElementById('temp_student_id').textContent = studentId;
    document.getElementById('temp_password_display').textContent = password;
    document.getElementById('tempPasswordModal').style.display = 'flex';
}

function closeTempPasswordModal() {
    document.getElementById('tempPasswordModal').style.display = 'none';
}

function deactivateStudent(id, name) {
    document.getElementById('deactivateForm').action = '/admin/students/' + id + '/deactivate';
    document.getElementById('deactivate_student_name').textContent = name;
    document.getElementById('deactivateModal').style.display = 'flex';
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').style.display = 'none';
}
</script>
@endsection
