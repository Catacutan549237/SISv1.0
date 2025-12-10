@extends('layouts.dashboard')

@section('title', 'Professors')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Professor Management</h1>
            <p class="page-subtitle">Manage professor accounts</p>
        </div>
        <form action="{{ route('admin.professors') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search professors..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.professors') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.professors', ['status' => 'active']) }}" class="btn {{ request('status', 'active') == 'active' ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.professors', ['status' => 'deactivated']) }}" class="btn {{ request('status') == 'deactivated' ? 'btn-primary' : 'btn-secondary' }}">Deactivated</a>
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

<!-- Add Professor Form -->
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Add New Professor</h2>
    
    <form method="POST" action="{{ route('admin.professors.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-input" required placeholder="e.g., Prof. Glenn Oliva">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-input" required placeholder="e.g., professor@university.com">
            </div>
        </div>
        
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <strong>Note:</strong> A temporary password will be generated automatically. You can view it later in the Actions menu.
        </div>
        
        <button type="submit" class="btn btn-primary">Add Professor</button>
    </form>
</div>

<!-- Professors List -->
<div class="table-container">
    <h2 class="card-title">All Professors ({{ $professors->total() }})</h2>
    
    @if($professors->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Assigned Sections</th>
                    <th>Password Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($professors as $professor)
                    <tr>
                        <td><strong>{{ $professor->name }}</strong></td>
                        <td>{{ $professor->email }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ $professor->course_sections_count }} sections
                            </span>
                        </td>
                        <td>
                            @if($professor->must_change_password)
                                <span class="badge badge-warning">Pending Change</span>
                            @else
                                <span class="badge badge-success">Password Changed</span>
                            @endif
                        </td>
                        <td>{{ $professor->created_at->format('M d, Y') }}</td>
                        <td>
                            <button onclick="editProfessor({{ $professor->id }}, '{{ $professor->name }}', '{{ $professor->email }}')" class="btn btn-primary btn-sm">Edit</button>
                            
                            @if($professor->must_change_password && $professor->temp_password)
                                <button onclick="showTempPassword('{{ $professor->name }}', '{{ $professor->temp_password }}')" class="btn btn-info btn-sm">View Temp Password</button>
                            @endif
                            
                            @if($professor->is_active)
                                <button onclick="deactivateProfessor({{ $professor->id }}, '{{ $professor->name }}')" class="btn btn-danger btn-sm">Deactivate</button>
                            @else
                                <form method="POST" action="{{ route('admin.professors.activate', $professor) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Activate</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">{{ $professors->links() }}</div>
    @else
        <div class="alert alert-info">No professors found.</div>
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 class="card-title">Edit Professor</h2>
        
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
            
            <button type="submit" class="btn btn-primary">Update Professor</button>
            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<!-- Temp Password Modal -->
<div id="tempPasswordModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; width: 90%;">
        <h2 class="card-title">Temporary Password</h2>
        
        <p><strong id="temp_professor_name"></strong></p>
        
        <div class="alert alert-info" style="margin: 20px 0;">
            <strong>Temporary Password:</strong>
            <div style="font-size: 24px; font-weight: 700; margin-top: 10px; color: var(--sage-green);" id="temp_password_display"></div>
        </div>
        
        <p style="color: var(--gray-text); font-size: 14px;">
            Share this password with the professor. They will be required to change it on first login.
        </p>
        
        <button type="button" onclick="closeTempPasswordModal()" class="btn btn-primary">Close</button>
    </div>
</div>

<!-- Deactivate Modal -->
<div id="deactivateModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; width: 90%;">
        <h2 class="card-title">Deactivate Professor</h2>
        
        <p>Are you sure you want to deactivate <strong id="deactivate_professor_name"></strong>?</p>
        
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
function editProfessor(id, name, email) {
    document.getElementById('editForm').action = '/admin/professors/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function showTempPassword(name, password) {
    document.getElementById('temp_professor_name').textContent = name;
    document.getElementById('temp_password_display').textContent = password;
    document.getElementById('tempPasswordModal').style.display = 'flex';
}

function closeTempPasswordModal() {
    document.getElementById('tempPasswordModal').style.display = 'none';
}

function deactivateProfessor(id, name) {
    document.getElementById('deactivateForm').action = '/admin/professors/' + id + '/deactivate';
    document.getElementById('deactivate_professor_name').textContent = name;
    document.getElementById('deactivateModal').style.display = 'flex';
}

function closeDeactivateModal() {
    document.getElementById('deactivateModal').style.display = 'none';
}
</script>
@endsection

