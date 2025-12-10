@extends('layouts.dashboard')

@section('title', 'Assessment Fees')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Assessment Fee Management</h1>
            <p class="page-subtitle">Manage tuition and miscellaneous fees</p>
        </div>
        <form action="{{ route('admin.assessments') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search fees..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.assessments') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.assessments') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.assessments', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
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

<!-- Add Assessment Fee Form -->
@if(!request('archived'))
<div class="card" style="margin-bottom: 30px;">
    <h2 class="card-title">Add New Assessment Fee</h2>
    
    <form method="POST" action="{{ route('admin.assessments.store') }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="charge_description" class="form-label">Charge Description</label>
                <input type="text" id="charge_description" name="charge_description" class="form-input" placeholder="e.g., Athletics, Library, Registration" required>
            </div>
            
            <div class="form-group">
                <label for="course" class="form-label">Course (Optional)</label>
                <input type="text" id="course" name="course" class="form-input" placeholder="Leave blank if not course-specific">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" id="amount" name="amount" class="form-input" step="0.01" min="0" placeholder="0.00" required>
            </div>
            
            <div class="form-group">
                <label for="order" class="form-label">Display Order</label>
                <input type="number" id="order" name="order" class="form-input" min="0" value="0" placeholder="0">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Assessment Fee</button>
    </form>
</div>
@endif

<!-- Assessment Fees List -->
<div class="table-container">
    <h2 class="card-title">All Assessment Fees ({{ $assessments->count() }})</h2>
    
    @if($assessments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Charge Description</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessments as $assessment)
                    <tr>
                        <td><strong>{{ $assessment->order }}</strong></td>
                        <td>{{ $assessment->charge_description }}</td>
                        <td>{{ $assessment->course ?? 'N/A' }}</td>
                        <td><strong>₱{{ number_format($assessment->amount, 2) }}</strong></td>
                        <td>

                            @if(request('archived'))
                                <form method="POST" action="{{ route('admin.assessments.restore', $assessment->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            @else
                                <button onclick="editAssessment({{ $assessment->id }}, '{{ $assessment->charge_description }}', '{{ $assessment->course }}', {{ $assessment->amount }}, {{ $assessment->order }})" class="btn btn-primary btn-sm">Edit</button>
                                <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this assessment fee?')">Archive</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Total Summary -->
        <div style="margin-top: 20px; padding: 15px; background: #f7fafc; border-radius: 8px; text-align: right;">
            <strong style="font-size: 1.1em;">Total Miscellaneous Fees: ₱{{ number_format($assessments->sum('amount'), 2) }}</strong>
        </div>
    @else
        <div class="alert alert-info">No assessment fees found.</div>
    @endif
</div>

<!-- Edit Modal (Simple) -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 class="card-title">Edit Assessment Fee</h2>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="edit_charge_description" class="form-label">Charge Description</label>
                <input type="text" id="edit_charge_description" name="charge_description" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_course" class="form-label">Course (Optional)</label>
                <input type="text" id="edit_course" name="course" class="form-input">
            </div>
            
            <div class="form-group">
                <label for="edit_amount" class="form-label">Amount</label>
                <input type="number" id="edit_amount" name="amount" class="form-input" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="edit_order" class="form-label">Display Order</label>
                <input type="number" id="edit_order" name="order" class="form-input" min="0">
            </div>
            
            <button type="submit" class="btn btn-primary">Update Assessment Fee</button>
            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editAssessment(id, chargeDescription, course, amount, order) {
    document.getElementById('editForm').action = '/admin/assessments/' + id;
    document.getElementById('edit_charge_description').value = chargeDescription;
    document.getElementById('edit_course').value = course || '';
    document.getElementById('edit_amount').value = amount;
    document.getElementById('edit_order').value = order;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
@endsection
