@extends('layouts.dashboard')

@section('title', 'My Payments')

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
    <h1 class="page-title">Payment History</h1>
    <p class="page-subtitle">View and manage your payments</p>
</div>

<div class="table-container">
    <h2 class="card-title">Payment Records</h2>
    
    @if($payments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->semester->name }}</td>
                        <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td><strong>₱{{ number_format($payment->balance, 2) }}</strong></td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($payment->status === 'partial')
                                <span class="badge badge-warning">Partial</span>
                            @else
                                <span class="badge badge-error">Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('student.payments.show', $payment) }}" class="btn btn-primary btn-sm">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            No payment records found.
        </div>
    @endif
</div>
@endsection
