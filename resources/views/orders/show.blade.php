@extends('layouts.app')

@section('title', $page_title ?? 'Order Details')

@section('page_content')
@php
    $totalAmount = $relatedOrders->sum('estimated_cost');
    $eventCount = $relatedOrders->count();
    $firstOrder = $relatedOrders->first();
    // Get current status - if all orders have same status, use it; otherwise use first order's status
    $statuses = $relatedOrders->pluck('orderStatus.id')->unique()->filter();
    $currentStatus = $statuses->count() === 1 ? $statuses->first() : ($firstOrder->orderStatus ? $firstOrder->orderStatus->id : null);
@endphp

<div class="row">
    <div class="col-lg-12">
        <!-- Summary Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $page_title ?? 'Order Details' }}</h4>
                <div class="d-flex gap-2 align-items-center" style="position: relative; z-index: 10;">
                    <a href="{{ route('orders.edit', $firstOrder) }}" class="btn btn-info btn-sm" style="pointer-events: auto; cursor: pointer;">
                        Edit
                    </a>
                    <button type="button" onclick="openStatusModal('{{ $firstOrder->id }}', '{{ $currentStatus }}')" class="btn btn-primary btn-sm" style="pointer-events: auto; cursor: pointer;">
                        Change Status
                    </button>
                    <a href="{{ route('orders.index') }}" style="pointer-events: auto; cursor: pointer;">
                        <i class="bi bi-arrow-left"></i>
                        Back to list
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Order Number :</span> <strong>{{ $order->order_number }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Customer :</span> <strong>{{ $order->customer->name }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Contact :</span> <strong>{{ $order->customer->mobile }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Total Amount :</span> <strong>₹{{ number_format($totalAmount, 2) }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Number of Events :</span> <strong>{{ $eventCount }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Address :</span> <strong>{{ $order->address }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <!-- Orders Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">All Orders</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatable table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="align-middle">Order Number</th>
                                <th class="align-middle">Event Date</th>
                                <th class="align-middle">Event Time</th>
                                <th class="align-middle">Event Menu</th>
                                <th class="align-middle">Guest Count</th>
                                <th class="align-middle">Order Type</th>
                                <th class="align-middle">Dish Price</th>
                                <th class="align-middle text-end">Cost</th>
                                <th class="align-middle text-end">Status</th>
                                <th class="align-middle text-end">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatedOrders as $relatedOrder)
                                <tr class="btn-reveal-trigger">
                                    <td class="py-2">
                                        <strong>{{ $relatedOrder->order_number }}</strong>
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->event_date ? $relatedOrder->event_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->eventTime ? $relatedOrder->eventTime->name : 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->event_menu ?? 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->guest_count ?? 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        {{ $relatedOrder->orderType ? $relatedOrder->orderType->name : 'N/A' }}
                                    </td>
                                    <td class="py-2">
                                        @if($relatedOrder->guest_count && $relatedOrder->estimated_cost)
                                            ₹{{ number_format($relatedOrder->estimated_cost / $relatedOrder->guest_count, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="py-2 text-end">
                                        <strong>₹{{ number_format($relatedOrder->estimated_cost ?? 0, 2) }}</strong>
                                    </td>
                                    <td class="py-2 text-end">
                                        @if($relatedOrder->orderStatus)
                                            <span class="badge badge-secondary">{{ $relatedOrder->orderStatus->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-end">
                                        @if($relatedOrder->payment_status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($relatedOrder->payment_status === 'partial')
                                            <span class="badge badge-warning">Partial</span>
                                        @elseif($relatedOrder->payment_status === 'pending')
                                            <span class="badge badge-danger">Pending</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($relatedOrder->payment_status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-end">
                                    <strong>Total:</strong>
                                </td>
                                <td class="text-end">
                                    <strong>₹{{ number_format($totalAmount, 2) }}</strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Get staff assigned to this order (using first order to get staff)
    $assignedStaff = $firstOrder->staff ?? collect();
@endphp

@hasPermission('staff.view')
<div class="row">
    <div class="col-lg-12">
        <!-- Assigned Staff Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Assigned Staff</h4>
                @hasPermission('staff.create')
                <a href="{{ route('staff.assign', $firstOrder) }}" class="btn btn-primary btn-sm">
                    Assign Staff
                </a>
                @endhasPermission
            </div>
            <div class="card-body">
                @if($assignedStaff->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Role (Event)</th>
                                    <th>Default Role</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedStaff as $staff)
                                    <tr>
                                        <td>
                                            <a href="{{ route('staff.show', $staff) }}">{{ $staff->name }}</a>
                                        </td>
                                        <td><a href="tel:{{ $staff->phone }}">{{ $staff->phone }}</a></td>
                                        <td><span class="badge badge-info light">{{ $staff->pivot->role ?? $staff->staff_role }}</span></td>
                                        <td><span class="badge badge-secondary light">{{ $staff->staff_role }}</span></td>
                                        <td>{{ $staff->pivot->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>{{ $assignedStaff->count() }}</strong> staff member(s) assigned to this order
                        </small>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-3">No staff assigned to this order yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endhasPermission

<!-- Status Update Modal -->
<div class="modal fade" id="status-modal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="status-update-form" class="needs-validation" action="" method="POST" novalidate>
                @csrf
                <div class="modal-body">
                    @include('error.alerts')

                    <div class="mb-3">
                        <label for="modal-order-status" class="form-label">Order Status <span class="text-danger">*</span></label>
                        <select name="order_status_id" id="modal-order-status" required class="form-control default-select">
                            <option value="">Select Status</option>
                            @foreach($orderStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Please select an order status.
                        </div>
                        @error('order_status_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
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

    let statusModal;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal
        const modalElement = document.getElementById('status-modal');
        if (modalElement) {
            statusModal = new bootstrap.Modal(modalElement);
        }
        
        // Ensure buttons are clickable
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(function(btn) {
            btn.style.pointerEvents = 'auto';
            btn.style.cursor = 'pointer';
        });
    });

    // Make function globally accessible
    window.openStatusModal = function(orderId, currentStatus) {
        try {
            // Set form action URL
            const form = document.getElementById('status-update-form');
            if (!form) {
                console.error('Status update form not found');
                return;
            }
            
            const routeUrl = '{{ route("orders.update-status", ":id") }}'.replace(':id', orderId);
            form.action = routeUrl;
            
            // Set current status in dropdown
            const selectElement = document.getElementById('modal-order-status');
            if (selectElement) {
                selectElement.value = currentStatus || '';
            }
            
            // Reset validation state
            form.classList.remove('was-validated');
            
            // Show modal
            if (statusModal) {
                statusModal.show();
            } else {
                // Reinitialize if needed
                const modalElement = document.getElementById('status-modal');
                if (modalElement) {
                    statusModal = new bootstrap.Modal(modalElement);
                    statusModal.show();
                }
            }
        } catch (error) {
            console.error('Error opening status modal:', error);
            alert('An error occurred while opening the status modal. Please try again.');
        }
    };
</script>
@endsection
