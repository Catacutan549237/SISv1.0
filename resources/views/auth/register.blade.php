@extends('layouts.auth')

@section('title', 'Register - SIS')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">SIS</div>
        <h1>Create Account</h1>
        <p>Register as a new student</p>
    </div>
    
    <div class="auth-body">
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-input @error('name') error @enderror" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus
                    placeholder="John Doe"
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') error @enderror" 
                    value="{{ old('email') }}" 
                    required
                    placeholder="your.email@university.com"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="program_id" class="form-label">Program (Optional)</label>
                <select 
                    id="program_id" 
                    name="program_id" 
                    class="form-input @error('program_id') error @enderror"
                >
                    <option value="">Select your program</option>
                    @foreach(\App\Models\Program::all() as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->name }} ({{ $program->code }})
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') error @enderror" 
                    required
                    placeholder="Minimum 8 characters"
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-input" 
                    required
                    placeholder="Re-enter your password"
                >
            </div>

            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>

        <div class="form-footer">
            <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
        </div>
    </div>
</div>
@endsection
