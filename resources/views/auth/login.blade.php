@extends('layouts.auth')

@section('title', 'Login - SIS')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <div class="auth-logo">SIS</div>
        <h1>Welcome Back</h1>
        <p>Sign in to access your account</p>
    </div>
    
    <div class="auth-body">
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
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

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input @error('password') error @enderror" 
                    required
                    placeholder="Enter your password"
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>

        <div class="form-footer">
            <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        </div>
    </div>
</div>
@endsection
