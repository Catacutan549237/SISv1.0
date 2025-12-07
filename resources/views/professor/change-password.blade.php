@extends('layouts.dashboard')

@section('title', 'Change Password')

@section('sidebar')
<div class="nav-item">
    <a href="{{ route('professor.dashboard') }}" class="nav-link">
        <span class="nav-icon">📊</span>
        <span>Dashboard</span>
    </a>
</div>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Change Your Password</h1>
    <p class="page-subtitle">You must change your password before continuing</p>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="alert alert-warning">
        <strong>Security Notice:</strong> You are using a temporary password. Please create a new secure password to continue.
    </div>
    
    <form method="POST" action="{{ route('professor.password.update') }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="current_password" class="form-label">Current (Temporary) Password</label>
            <input type="password" id="current_password" name="current_password" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <input type="password" id="password" name="password" class="form-input" required minlength="8">
            <small style="color: var(--gray-text);">Minimum 8 characters</small>
        </div>
        
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
</div>
@endsection
