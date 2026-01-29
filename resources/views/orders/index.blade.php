@extends('layouts.app')

@section('title', $page_title ?? 'Orders')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="card-title mb-0">{{ $page_title ?? 'Orders' }}</h4>
                    </div>
                    @if(isset($subtitle))
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                        </div>
                    @endif
                </div>
                @hasPermission('orders.create')
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary btn-add">Create Order</a>
                @endhasPermission
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('orders.index') }}" class="mb-4">
                    <!-- Preserve sort parameters -->
                    @if(request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if(request('sort_order'))
                        <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                    @endif
                    
                    <div class="row g-2 align-items-end mb-3">
                        <!-- Status Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="status_filter" class="form-label">Status</label>
                            <select name="status[]" id="status_filter" class="form-control form-control-sm multi-select-sm" multiple>
                                <option value="pending" {{ in_array('pending', $filterValues['status'] ?? []) ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ in_array('confirmed', $filterValues['status'] ?? []) ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ in_array('completed', $filterValues['status'] ?? []) ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ in_array('cancelled', $filterValues['status'] ?? []) ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Payment Status Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="payment_status_filter" class="form-label">Payment Status</label>
                            <select name="payment_status[]" id="payment_status_filter" class="form-control form-control-sm multi-select-sm" multiple>
                                <option value="pending" {{ in_array('pending', $filterValues['payment_status'] ?? []) ? 'selected' : '' }}>Pending</option>
                                <option value="partial" {{ in_array('partial', $filterValues['payment_status'] ?? []) ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ in_array('paid', $filterValues['payment_status'] ?? []) ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <!-- Event Time Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="event_time_filter" class="form-label">Event Time</label>
                            <select name="event_time[]" id="event_time_filter" class="form-control form-control-sm multi-select-sm" multiple>
                                <option value="morning" {{ in_array('morning', $filterValues['event_time'] ?? []) ? 'selected' : '' }}>Morning</option>
                                <option value="afternoon" {{ in_array('afternoon', $filterValues['event_time'] ?? []) ? 'selected' : '' }}>Afternoon</option>
                                <option value="evening" {{ in_array('evening', $filterValues['event_time'] ?? []) ? 'selected' : '' }}>Evening</option>
                                <option value="night_snack" {{ in_array('night_snack', $filterValues['event_time'] ?? []) ? 'selected' : '' }}>Night Snack</option>
                            </select>
                        </div>

                        <!-- Order Type Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="order_type_filter" class="form-label">Order Type</label>
                            <select name="order_type[]" id="order_type_filter" class="form-control form-control-sm multi-select-sm" multiple>
                                <option value="full_service" {{ in_array('full_service', $filterValues['order_type'] ?? []) ? 'selected' : '' }}>Full Service</option>
                                <option value="preparation_only" {{ in_array('preparation_only', $filterValues['order_type'] ?? []) ? 'selected' : '' }}>Preparation Only</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 align-items-end mb-3">
                        <!-- Event Date Range -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="event_date_from" class="form-label">Event Date From</label>
                            <input type="date" name="event_date_between[from]" id="event_date_from" value="{{ ($filterValues['event_date_between'] ?? [])['from'] ?? '' }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="event_date_to" class="form-label">Event Date To</label>
                            <input type="date" name="event_date_between[to]" id="event_date_to" value="{{ ($filterValues['event_date_between'] ?? [])['to'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <!-- Customer Search -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="customer_search" class="form-label">Customer Search</label>
                            <input type="text" name="customer_search" id="customer_search" value="{{ $filterValues['customer_search'] ?? '' }}" class="form-control form-control-sm" placeholder="Name, Mobile, or Email">
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <x-filter-buttons resetRoute="{{ route('orders.index') }}" />
                </form>
                <hr>
                <div class="table-responsive">
                    <table id="ordersTable" class="datatable table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <x-table.sort-link field="customer.name" label="Customer Name" />
                                </th>
                                <th>
                                    <x-table.sort-link field="customer.mobile" label="Contact Number" />
                                </th>
                                <th>
                                    <x-table.sort-link field="customer.email" label="Email" />
                                </th>
                                <th>
                                    <x-table.sort-link field="payment_status" label="Payment Status" />
                                </th>
                                <th>
                                    <x-table.sort-link field="total_amount" label="Amount" />
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $group)
                                @php
                                    $firstOrder = $group['orders']->first();
                                    $status = $group['status'];
                                    $paymentStatus = $group['payment_status'];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3" style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #c74e36 0%, #e04c16 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                {{ strtoupper(substr($group['customer']->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $group['customer']->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $group['customer']->mobile }}</td>
                                    <td>{{ $group['customer']->email }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $paymentStatus === 'paid' ? 'badge-success' : '' }}
                                            {{ $paymentStatus === 'partial' ? 'badge-warning' : '' }}
                                            {{ $paymentStatus === 'pending' ? 'badge-danger' : '' }}
                                            {{ $paymentStatus === 'mixed' ? 'badge-info' : '' }}">
                                            {{ ucfirst($paymentStatus) }}
                                        </span>
                                    </td>
                                    <td><strong>₹{{ number_format($group['total_amount'], 2) }}</strong></td>
                                    <td>
                                        <a href="{{ route('orders.show', $firstOrder) }}" class="btn btn-primary btn-xs" title="View">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted">No orders found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for multiselect dropdowns
        $('#status_filter, #payment_status_filter, #event_time_filter, #order_type_filter').select2({
            placeholder: '',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
