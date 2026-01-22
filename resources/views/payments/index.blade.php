@extends('layouts.app')

@section('title', 'Payments')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payments</h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Filter Form -->
                <form method="GET" action="{{ route('payments.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end mb-3">
                        <!-- Name Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="name_filter" class="form-label">Name</label>
                            <input type="text" name="name_like" id="name_filter" value="{{ $filterValues['name_like'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <!-- Contact Number Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="mobile_filter" class="form-label">Contact Number</label>
                            <input type="text" name="mobile_like" id="mobile_filter" value="{{ $filterValues['mobile_like'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <!-- Payment Status Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="payment_status_filter" class="form-label">Payment Status</label>
                            <select name="payment_status" id="payment_status_filter" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="pending" {{ ($filterValues['payment_status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="partial" {{ ($filterValues['payment_status'] ?? '') == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ ($filterValues['payment_status'] ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="mixed" {{ ($filterValues['payment_status'] ?? '') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <x-filter-buttons resetRoute="{{ route('payments.index') }}" />
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Contact Number</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $group)
                                @php
                                    $paymentStatus = $group['payment_status'];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3" style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                {{ strtoupper(substr($group['customer']->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $group['customer']->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $group['customer']->mobile }}</td>
                                    <td><strong>₹{{ number_format($group['total_amount'], 2) }}</strong></td>
                                    <td>
                                        <span class="badge 
                                            {{ $paymentStatus === 'paid' ? 'badge-success' : '' }}
                                            {{ $paymentStatus === 'partial' ? 'badge-warning' : '' }}
                                            {{ $paymentStatus === 'pending' ? 'badge-danger' : '' }}
                                            {{ $paymentStatus === 'mixed' ? 'badge-info' : '' }}">
                                            {{ ucfirst($paymentStatus) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($group['invoice'])
                                                <a href="{{ route('invoices.show', $group['invoice']) }}" class="btn btn-success btn-sm" title="View Invoice">
                                                    <i class="bi bi-eye me-1"></i>View Invoice
                                                </a>
                                                <a href="{{ route('invoices.download', $group['invoice']) }}" class="btn btn-primary btn-sm" title="Download PDF">
                                                    <i class="bi bi-download me-1"></i>Download PDF
                                                </a>
                                            @else
                                                <a href="{{ route('invoices.generate', $group['order_number']) }}" class="btn btn-info btn-sm" title="Generate Invoice">
                                                    <i class="bi bi-file-earmark-plus me-1"></i>Generate Invoice
                                                </a>
                                            @endif
                                            @if($group['orders']->count() > 1)
                                                <button type="button" onclick="openPaymentModal('{{ $group['order_number'] }}', {{ $group['orders']->count() }}, '{{ $paymentStatus }}')" class="btn btn-primary btn-sm" title="Update Payment">
                                                    <i class="bi bi-pencil me-1"></i>Update Payment
                                                </button>
                                            @else
                                                <a href="{{ route('orders.edit', $group['orders']->first()) }}" class="btn btn-primary btn-sm" title="Update Payment">
                                                    <i class="bi bi-pencil me-1"></i>Update Payment
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted">No payments found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($orders, 'links'))
                    <div class="mt-3">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Update Modal -->
<div class="modal fade" id="payment-modal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payment-update-form" class="needs-validation" action="{{ route('payments.update-group') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>There were errors with your submission:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="modal-order-number" class="form-label">Order Number <span class="text-danger">*</span></label>
                        <input type="text" id="modal-order-number" readonly class="form-control bg-light">
                        <input type="hidden" name="order_number" id="hidden-order-number">
                        <div class="invalid-feedback">
                            Order number is required.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="modal-order-count" class="form-label">Number of Orders</label>
                        <input type="text" id="modal-order-count" readonly class="form-control bg-light">
                    </div>
                    
                    <div class="mb-3">
                        <label for="modal-payment-status" class="form-label">Payment Status <span class="text-danger">*</span></label>
                        <select name="payment_status" id="modal-payment-status" required class="form-control default-select">
                            <option value="">Select Payment Status</option>
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a payment status.
                        </div>
                        @error('payment_status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Payment Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()

    let paymentModal;

    document.addEventListener('DOMContentLoaded', function() {
        paymentModal = new bootstrap.Modal(document.getElementById('payment-modal'));
    });

    function openPaymentModal(orderNumber, orderCount, currentStatus) {
        // Set form values
        document.getElementById('modal-order-number').value = orderNumber;
        document.getElementById('hidden-order-number').value = orderNumber;
        document.getElementById('modal-order-count').value = orderCount + ' order(s)';
        
        // Set payment status, defaulting to 'pending' if 'mixed'
        const selectElement = document.getElementById('modal-payment-status');
        selectElement.value = currentStatus === 'mixed' ? 'pending' : currentStatus;
        
        // Reset validation state
        const form = document.getElementById('payment-update-form');
        form.classList.remove('was-validated');
        
        // Show modal
        if (paymentModal) {
            paymentModal.show();
        }
    }
</script>
@endsection
