@extends('layouts.dashboard')

@section('title', 'Payments')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Payment Management</h1>
            <p class="page-subtitle">View and update student payments</p>
        </div>
        <form action="{{ route('admin.payments') }}" method="GET" style="display: flex; gap: 10px;">
            <input type="text" name="search" class="form-input" placeholder="Search payments..." value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary">Search</button>
            @if($search)
                <a href="{{ route('admin.payments') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="table-container">
    <h2 class="card-title">All Payments ({{ $payments->total() }})</h2>
    @if($payments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Student</th>
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
                        <td>{{ $payment->student->student_id }} - {{ $payment->student->name }}</td>
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
                            <button onclick="updatePayment({{ $payment->id }}, {{ $payment->amount_paid }}, '{{ $payment->payment_method }}', '{{ $payment->reference_number }}')" class="btn btn-primary btn-sm">Update</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $payments->links() }}</div>
    @else
        <div class="alert alert-info">No payments found.</div>
    @endif
</div>

<!-- Update Payment Modal -->
<div id="updateModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; width: 90%;">
        <h2 class="card-title">Update Payment</h2>
        <form id="updateForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Amount Paid</label>
                <input type="number" id="amount_paid" name="amount_paid" class="form-input" required step="0.01" min="0">
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method</label>
                <select id="payment_method" name="payment_method" class="form-input">
                    <option value="">Select Method</option>
                    <option value="cash">Cash</option>
                    <option value="online">Online Payment</option>
                    <option value="card">Credit/Debit Card</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Reference Number</label>
                <input type="text" id="reference_number" name="reference_number" class="form-input">
            </div>
            <button type="submit" class="btn btn-primary">Update Payment</button>
            <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updatePayment(id, amountPaid, method, ref) {
    document.getElementById('updateForm').action = '/admin/payments/' + id;
    document.getElementById('amount_paid').value = amountPaid;
    document.getElementById('payment_method').value = method || '';
    document.getElementById('reference_number').value = ref || '';
    document.getElementById('updateModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('updateModal').style.display = 'none';
}
</script>
@endsection

