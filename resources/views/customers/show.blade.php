@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Customer Details</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Customer Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <p class="mb-0"><span class="text-muted">Name :</span> <strong>{{ $customer->name }}</strong></p>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <p class="mb-0"><span class="text-muted">Mobile :</span> <strong>{{ $customer->mobile }}</strong></p>
                        </div>
                        @if($customer->email)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <p class="mb-0"><span class="text-muted">Email :</span> <strong>{{ $customer->email }}</strong></p>
                            </div>
                        @endif
                        @if($customer->address)
                        <div class="col-lg-4 col-md-6 mb-3">
                                <p class="mb-0"><span class="text-muted">Address :</span> <strong>{{ $customer->address }}</strong></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order History</h4>
                </div>
                <div class="card-body">
                    @if($groupedOrdersList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Event Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupedOrdersList as $group)
                                        @php
                                            $firstOrder = $group['orders']->first();
                                            $status = $group['status'];
                                            $paymentStatus = $group['payment_status'];
                                            $orderNumber = $group['order_number'];
                                        @endphp
                                        <tr class="btn-reveal-trigger">
                                            <td class="py-2">
                                                <strong>{{ $orderNumber }}</strong>
                                                @if($group['orders']->count() > 1)
                                                    <span class="text-muted small">({{ $group['orders']->count() }} orders)</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                {{ $group['event_date'] ? $group['event_date']->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="py-2">
                                                <strong>₹{{ number_format($group['total_amount'], 2) }}</strong>
                                            </td>
                                            <td class="py-2">
                                                @if($status === 'confirmed')
                                                    <span class="badge light badge-success">{{ ucfirst($status) }}</span>
                                                @elseif($status === 'pending')
                                                    <span class="badge light badge-warning">{{ ucfirst($status) }}</span>
                                                @elseif($status === 'completed')
                                                    <span class="badge light badge-info">{{ ucfirst($status) }}</span>
                                                @elseif($status === 'cancelled')
                                                    <span class="badge light badge-danger">{{ ucfirst($status) }}</span>
                                                @elseif($status === 'mixed')
                                                    <span class="badge light badge-primary">{{ ucfirst($status) }}</span>
                                                @else
                                                    <span class="badge light badge-secondary">{{ ucfirst($status) }}</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                @if($paymentStatus === 'paid')
                                                    <span class="badge light badge-success">{{ ucfirst($paymentStatus) }}</span>
                                                @elseif($paymentStatus === 'partial')
                                                    <span class="badge light badge-warning">{{ ucfirst($paymentStatus) }}</span>
                                                @elseif($paymentStatus === 'pending')
                                                    <span class="badge light badge-danger">{{ ucfirst($paymentStatus) }}</span>
                                                @elseif($paymentStatus === 'mixed')
                                                    <span class="badge light badge-primary">{{ ucfirst($paymentStatus) }}</span>
                                                @else
                                                    <span class="badge light badge-secondary">{{ ucfirst($paymentStatus) }}</span>
                                                @endif
                                            </td>
                                            <td class="py-2 text-end">
                                                <a href="{{ route('orders.show', $firstOrder) }}" class="btn btn-primary btn-sm" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No orders found for this customer</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
