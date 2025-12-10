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
    <a href="{{ route('student.assessments') }}" class="nav-link">
        <span class="nav-icon">📋</span>
        <span>Assessment</span>
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

<!-- Assessment Breakdown -->
<div class="table-container" style="margin-bottom: 30px;">
    <h2 class="card-title">Breakdown of Fees</h2>
    
    <table>
        <thead>
            <tr>
                <th>Charge Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- Per Unit Fee -->
            <tr style="background: #f7fafc;">
                <td><strong>Tuition Fee ({{ number_format($perUnitFee, 2) }} X {{ number_format($totalUnits, 1) }} units)</strong></td>
                <td style="text-align: right;"><strong>₱{{ number_format($perUnitTotal, 2) }}</strong></td>
            </tr>
            
            <!-- Miscellaneous Fees -->
            @foreach($assessmentFees as $fee)
                <tr>
                    <td>{{ $fee->charge_description }}</td>
                    <td style="text-align: right;">₱{{ number_format($fee->amount, 2) }}</td>
                </tr>
            @endforeach
            
            <!-- Total Row -->
            <tr style="background: #e6f4ea; font-weight: bold; font-size: 1.1em;">
                <td style="text-align: right; padding-right: 20px;">
                    <strong>TOTAL ASSESSMENT:</strong>
                </td>
                <td style="text-align: right;">
                    <strong>₱{{ number_format($payment->total_amount, 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Payment Form -->
@if($payment->balance > 0)
<div class="card">
    <h2 class="card-title">Make a Payment</h2>
    
    <div class="alert alert-info">
        <strong>Note:</strong> This is a placeholder for online payment integration, this does not have a payment gateway.
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
                <option value="card">Credit/Debit Card</option>
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
