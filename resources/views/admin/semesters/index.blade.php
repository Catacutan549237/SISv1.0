@extends('layouts.dashboard')

@section('title', 'Semesters')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Semester Management</h1>
            <p class="page-subtitle">Manage academic semesters</p>
        </div>
        <form action="{{ route('admin.semesters') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search semesters..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.semesters') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('admin.semesters') }}" class="btn {{ !request('archived') ? 'btn-primary' : 'btn-secondary' }}">Active</a>
    <a href="{{ route('admin.semesters', ['archived' => 1]) }}" class="btn {{ request('archived') ? 'btn-primary' : 'btn-secondary' }}">Archived</a>
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
    <h2 class="card-title">Add New Semester</h2>
    <form method="POST" action="{{ route('admin.semesters.store') }}">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Semester Name</label>
                <input type="text" name="name" class="form-input" required placeholder="e.g., First Semester 2025-26">
            </div>
            <div class="form-group">
                <label class="form-label">Semester Code</label>
                <input type="text" name="code" class="form-input" required placeholder="e.g., 1-2025-26">
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Set as Current</label>
                <select name="is_current" class="form-input">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Semester</button>
    </form>
</div>
@endif

<div class="table-container">
    <h2 class="card-title">All Semesters ({{ $semesters->count() }})</h2>
    @if($semesters->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semesters as $semester)
                    <tr>
                        <td><strong>{{ $semester->name }}</strong></td>
                        <td>{{ $semester->code }}</td>
                        <td>{{ $semester->start_date->format('M d, Y') }}</td>
                        <td>{{ $semester->end_date->format('M d, Y') }}</td>
                        <td>
                            @if($semester->is_current)
                                <span class="badge badge-success">Current</span>
                            @else
                                <span class="badge badge-warning">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if(request('archived'))
                                <form method="POST" action="{{ route('admin.semesters.restore', $semester->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            @else
                                @if(!$semester->is_current)
                                    <form method="POST" action="{{ route('admin.semesters.set-current', $semester) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Set as Current</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.semesters.destroy', $semester) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Archive this semester?')">Archive</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No semesters found.</div>
    @endif
</div>
@endsection

