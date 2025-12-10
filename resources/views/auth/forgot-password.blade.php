@extends('layouts.auth')

@section('title', 'Forgot Password - SIS')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">SIS</div>
        <h1>Forgot Password?</h1>
        <p>Enter your email address and we'll send you a reset link</p>
    </div>
    
    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') error @enderror" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="your.email@university.com"
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>

        <div class="form-footer">
            <p>Remember your password? <a href="{{ route('login') }}">Back to Login</a></p>
        </div>
    </div>
</div>
@endsection
