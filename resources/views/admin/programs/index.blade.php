@extends('layouts.dashboard')

@section('title', 'Programs')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Program Management</h1>
            <p class="page-subtitle">Manage academic programs with unit limits</p>
        </div>
        <form action="{{ route('admin.programs') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search programs..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.programs') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.programs') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.programs', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
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

<!-- Add Program Form -->
@if(!request('archived'))
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Add New Program</h2>
    
    <form method="POST" action="{{ route('admin.programs.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="department_id" class="form-label">Department</label>
                <select id="department_id" name="department_id" class="form-input" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="code" class="form-label">Program Code</label>
                <input type="text" id="code" name="code" class="form-input" required placeholder="e.g., BSIT">
            </div>
        </div>
        
        <div class="form-group">
            <label for="name" class="form-label">Program Name</label>
            <input type="text" id="name" name="name" class="form-input" required placeholder="e.g., Bachelor of Science in Information Technology">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="min_units" class="form-label">Minimum Units</label>
                <input type="number" id="min_units" name="min_units" class="form-input" required value="18" min="1">
            </div>
            
            <div class="form-group">
                <label for="max_units" class="form-label">Maximum Units</label>
                <input type="number" id="max_units" name="max_units" class="form-input" required value="24" min="1">
            </div>
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-input" rows="3"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Program</button>
    </form>
</div>
@endif

<!-- Programs List -->
<div class="table-container">
    <h2 class="card-title">All Programs ({{ $programs->count() }})</h2>
    
    @if($programs->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Min Units</th>
                    <th>Max Units</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programs as $program)
                    <tr>
                        <td><strong>{{ $program->code }}</strong></td>
                        <td>{{ $program->name }}</td>
                        <td>{{ $program->department->code }}</td>
                        <td><span class="badge badge-info">{{ $program->min_units }}</span></td>
                        <td><span class="badge badge-warning">{{ $program->max_units }}</span></td>
                        <td>

                            @if(request('archived'))
                                <form method="POST" action="{{ route('admin.programs.restore', $program->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            @else
                                <button onclick="editProgram({{ $program }})" class="btn btn-primary btn-sm">Edit</button>
                                <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this program?')">Archive</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No programs found.</div>
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 class="card-title">Edit Program</h2>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="edit_department_id" class="form-label">Department</label>
                <select id="edit_department_id" name="department_id" class="form-input" required>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_name" class="form-label">Program Name</label>
                <input type="text" id="edit_name" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_code" class="form-label">Program Code</label>
                <input type="text" id="edit_code" name="code" class="form-input" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="edit_min_units" class="form-label">Minimum Units</label>
                    <input type="number" id="edit_min_units" name="min_units" class="form-input" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="edit_max_units" class="form-label">Maximum Units</label>
                    <input type="number" id="edit_max_units" name="max_units" class="form-input" required min="1">
                </div>
            </div>
            
            <div class="form-group">
                <label for="edit_description" class="form-label">Description</label>
                <textarea id="edit_description" name="description" class="form-input" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Program</button>
            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editProgram(program) {
    document.getElementById('editForm').action = '/admin/programs/' + program.id;
    document.getElementById('edit_department_id').value = program.department_id;
    document.getElementById('edit_name').value = program.name;
    document.getElementById('edit_code').value = program.code;
    document.getElementById('edit_min_units').value = program.min_units;
    document.getElementById('edit_max_units').value = program.max_units;
    document.getElementById('edit_description').value = program.description || '';
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endsection

