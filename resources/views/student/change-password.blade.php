@extends('layouts.dashboard')

@section('title', 'Change Password')

@section('sidebar')
<div class="nav-item">
    <a href="{{ route('student.dashboard') }}" class="nav-link">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.enrollment') }}" class="nav-link">
        <span class="nav-icon">📝</span>
        <span>Enroll Course</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.my-enrollment') }}" class="nav-link">
        <span class="nav-icon">📚</span>
        <span>Class Schedule</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.assessments') }}" class="nav-link">
        <span class="nav-icon">📋</span>
        <span>Assessment</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.payments') }}" class="nav-link">
        <span class="nav-icon">💳</span>
        <span>Online Payment</span>
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('student.grades') }}" class="nav-link">
        <span class="nav-icon">🎓</span>
        <span>Evaluation</span>
    </a>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Change Password</h1>
    <p class="page-subtitle">Update your account password</p>
</div>

@if(auth()->user()->must_change_password)
<div class="alert alert-warning" style="max-width: 600px;">
    <strong>Security Notice:</strong> You are using a temporary password. Please create a new secure password to continue.
</div>
@endif

<div class="card" style="max-width: 600px;">
    <h2 class="card-title">Change Your Password</h2>
    
    <form method="POST" action="{{ route('student.password.update') }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="current_password" class="form-label">Current Password</label>
            <input 
                type="password" 
                id="current_password" 
                name="current_password" 
                class="form-input @error('current_password') error @enderror" 
                required
                placeholder="Enter your current password"
            >
            @error('current_password')
                <span class="error-message" style="color: var(--error-red); font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-input @error('password') error @enderror" 
                required
                placeholder="Enter new password (min. 8 characters)"
            >
            @error('password')
                <span class="error-message" style="color: var(--error-red); font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                class="form-input" 
                required
                placeholder="Re-enter new password"
            >
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Update Password</button>
            @if(!auth()->user()->must_change_password)
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection
