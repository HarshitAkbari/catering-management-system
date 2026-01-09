@extends('layouts.app')

@section('title', 'Orders')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Orders</h4>
                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Order
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ordersTable" class="datatable table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th>Payment Status</th>
                                <th>Amount</th>
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
                                            <div class="avatar-sm me-3" style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
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
                                        <a href="{{ route('orders.show', $firstOrder) }}" class="btn btn-primary btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted">No orders found</p>
                                        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm mt-2">Create First Order</a>
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

