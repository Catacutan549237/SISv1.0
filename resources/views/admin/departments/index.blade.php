@extends('layouts.dashboard')

@section('title', 'Departments')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Department Management</h1>
            <p class="page-subtitle">Manage academic departments</p>
        </div>
        <form action="{{ route('admin.departments') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search departments..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.departments') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.departments') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.departments', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
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

<!-- Add Department Form -->
@if(!request('archived'))
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Add New Department</h2>
    
    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="name" class="form-label">Department Name</label>
                <input type="text" id="name" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="code" class="form-label">Department Code</label>
                <input type="text" id="code" name="code" class="form-input" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Department</button>
    </form>
</div>
@endif

<!-- Departments List -->
<div class="table-container">
    <h2 class="card-title">All Departments ({{ $departments->count() }})</h2>
    
    @if($departments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Programs</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $department)
                    <tr>
                        <td><strong>{{ $department->code }}</strong></td>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->description ?? 'N/A' }}</td>
                        <td><span class="badge badge-info">{{ $department->programs_count }} programs</span></td>
                        <td>

                            @if(request('archived'))
                                <form method="POST" action="{{ route('admin.departments.restore', $department->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            @else
                                <button onclick="editDepartment({{ $department->id }}, '{{ $department->name }}', '{{ $department->code }}', '{{ $department->description }}')" class="btn btn-primary btn-sm">Edit</button>
                                <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this department?')">Archive</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No departments found.</div>
    @endif
</div>

<!-- Edit Modal (Simple) -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 class="card-title">Edit Department</h2>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="edit_name" class="form-label">Department Name</label>
                <input type="text" id="edit_name" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_code" class="form-label">Department Code</label>
                <input type="text" id="edit_code" name="code" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_description" class="form-label">Description</label>
                <textarea id="edit_description" name="description" class="form-input" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Department</button>
            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editDepartment(id, name, code, description) {
    document.getElementById('editForm').action = '/admin/departments/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endsection

