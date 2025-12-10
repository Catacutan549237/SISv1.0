@extends('layouts.dashboard')

@section('title', 'Assessment')

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
    <a href="{{ route('student.assessments') }}" class="nav-link active">
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
    <h1 class="page-title">Tuition Fee Assessment</h1>
    <p class="page-subtitle">{{ $student->name }} - {{ $student->student_id }}</p>
    @if($currentSemester)
        <p class="page-subtitle" style="font-weight: 600; color: var(--sage-green);">
            {{ $currentSemester->name }}
        </p>
    @endif
</div>

<!-- Assessment Summary Cards -->
@if($totalUnits > 0)
<div class="card-grid" style="margin-bottom: 30px;">
    <div class="card stat-card" style="border-left-color: var(--sage-green);">
        <div class="stat-label">Total Units Enrolled</div>
        <div class="stat-value">{{ $totalUnits }}</div>
    </div>
    <div class="card stat-card warning">
        <div class="stat-label">Per Unit Fee</div>
        <div class="stat-value">₱{{ number_format($perUnitFee, 2) }}</div>
    </div>
    <div class="card stat-card info">
        <div class="stat-label">Total Assessment</div>
        <div class="stat-value">₱{{ number_format($totalAssessment, 2) }}</div>
    </div>
</div>
@else
<div class="alert alert-warning" style="margin-bottom: 30px;">
    <strong>No Enrollment Found</strong><br>
    You haven't enrolled in any courses yet. Please go to <a href="{{ route('student.enrollment') }}" style="color: var(--sage-green); font-weight: bold;">Enroll Course</a> to select your courses for this semester.
</div>
@endif

<!-- Assessment Breakdown Table -->
<div class="table-container">
    <h2 class="card-title">Assessment Breakdown</h2>
    
    @if($totalUnits > 0)
        <table>
            <thead>
                <tr>
                    <th>Charge Description</th>
                    <th>Course</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- Per Unit Fee -->
                <tr style="background: #f7fafc;">
                    <td><strong>Per Unit Fee ({{ number_format($perUnitFee, 2) }} X {{ number_format($totalUnits, 1) }})</strong></td>
                    <td></td>
                    <td style="text-align: right;"><strong>₱{{ number_format($perUnitTotal, 2) }}</strong></td>
                </tr>
                
                <!-- Miscellaneous Fees -->
                @foreach($assessmentFees as $fee)
                    <tr>
                        <td>{{ $fee->charge_description }}</td>
                        <td>{{ $fee->course ?? '' }}</td>
                        <td style="text-align: right;">₱{{ number_format($fee->amount, 2) }}</td>
                    </tr>
                @endforeach
                
                <!-- Total Row -->
                <tr style="background: #e6f4ea; font-weight: bold; font-size: 1.1em;">
                    <td colspan="2" style="text-align: right; padding-right: 20px;">
                        <strong>TOTAL ASSESSMENT:</strong>
                    </td>
                    <td style="text-align: right;">
                        <strong>₱{{ number_format($totalAssessment, 2) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            <strong>No assessment available.</strong> Please enroll in courses first to view your assessment.
        </div>
    @endif
</div>

<!-- Payment Information -->
@if($totalUnits > 0 && $totalAssessment > 0)
<div class="card" style="margin-top: 30px;">
    <h2 class="card-title">Payment Information</h2>
    <div style="padding: 15px; background: #f7fafc; border-radius: 8px; margin-bottom: 15px;">
        <p style="margin: 0 0 10px 0;"><strong>Total Amount Due:</strong> ₱{{ number_format($totalAssessment, 2) }}</p>
        <p style="margin: 0; color: #666;">Please proceed to the <strong>Online Payment</strong> section to pay your tuition fees.</p>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('student.payments') }}" class="btn btn-primary" style="font-size: 1.1em; padding: 12px 30px;">
            Proceed to Payment
        </a>
    </div>
</div>
@endif

<style>
@media print {
    .sidebar, .btn, .page-header .page-subtitle:last-child {
        display: none !important;
    }
    
    .table-container, .card {
        box-shadow: none !important;
        border: 1px solid #ddd;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endsection
