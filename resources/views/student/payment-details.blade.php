@extends('layouts.dashboard')

@section('title', 'Payment Details')

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
    <a href="{{ route('student.payments') }}" class="nav-link active">
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
    <h1 class="page-title">Payment Details</h1>
    <p class="page-subtitle">{{ $payment->semester->name }}</p>
</div>

<!-- Payment Summary -->
<div class="card-grid">
    <div class="card stat-card">
        <div class="stat-label">Total Amount</div>
        <div class="stat-value">₱{{ number_format($payment->total_amount, 2) }}</div>
    </div>
    <div class="card stat-card" style="border-left-color: var(--success-green);">
        <div class="stat-label">Amount Paid</div>
        <div class="stat-value">₱{{ number_format($payment->amount_paid, 2) }}</div>
    </div>
    <div class="card stat-card error">
        <div class="stat-label">Balance</div>
        <div class="stat-value">₱{{ number_format($payment->balance, 2) }}</div>
    </div>
</div>

<!-- Enrolled Courses -->
<div class="table-container" style="margin-bottom: 30px;">
    <h2 class="card-title">Enrolled Courses</h2>
    
    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Units</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $enrollment)
                <tr>
                    <td><strong>{{ $enrollment->courseSection->course->course_code }}</strong></td>
                    <td>{{ $enrollment->courseSection->course->name }}</td>
                    <td>{{ $enrollment->courseSection->course->units }}</td>
                    <td>₱{{ number_format($enrollment->courseSection->course->units * 3850, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Payment Form -->
@if($payment->balance > 0)
<div class="card">
    <h2 class="card-title">Make a Payment</h2>
    
    <div class="alert alert-info">
        <strong>Note:</strong> This is a placeholder for online payment integration. In a production system, this would integrate with a payment gateway like PayMongo, PayPal, or Stripe.
    </div>
    
    <form method="POST" action="{{ route('student.payments.process', $payment) }}">
        @csrf
        
        <div class="form-group">
            <label for="amount" class="form-label">Payment Amount</label>
            <input 
                type="number" 
                id="amount" 
                name="amount" 
                class="form-input" 
                value="{{ $payment->balance }}"
                min="1"
                max="{{ $payment->balance }}"
                step="0.01"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select id="payment_method" name="payment_method" class="form-input" required>
                <option value="">Select payment method</option>
                <option value="online">Online Payment</option>
                <option value="cash">Cash</option>
                <option value="check">Check</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Process Payment</button>
        <a href="{{ route('student.payments') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@else
<div class="alert alert-success">
    <strong>Payment Complete!</strong> You have fully paid for this semester.
</div>
<a href="{{ route('student.payments') }}" class="btn btn-secondary">Back to Payments</a>
@endif
@endsection
