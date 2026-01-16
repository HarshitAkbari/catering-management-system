@extends('layout.default')

@section('title', 'Equipment Details')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('equipment.index') }}">Equipment</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Equipment Details</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ $equipment->name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('equipment.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 mb-3">
                            <p class="mb-0"><span class="text-muted">Name :</span> <strong>{{ $equipment->name }}</strong></p>
                        </div>
                        @if($equipment->category)
                            <div class="col-lg-6 col-md-6 mb-3">
                                <p class="mb-0"><span class="text-muted">Category :</span> <strong>{{ $equipment->category }}</strong></p>
                            </div>
                        @endif
                        <div class="col-lg-6 col-md-6 mb-3">
                            <p class="mb-0"><span class="text-muted">Total Quantity :</span> <strong>{{ $equipment->quantity }}</strong></p>
                        </div>
                        <div class="col-lg-6 col-md-6 mb-3">
                            <p class="mb-0"><span class="text-muted">Available Quantity :</span> <strong>{{ $equipment->available_quantity }}</strong></p>
                        </div>
                        <div class="col-lg-6 col-md-6 mb-3">
                            <p class="mb-0"><span class="text-muted">Status :</span> 
                                @if($equipment->status === 'available')
                                    <span class="badge light badge-success">{{ ucfirst($equipment->status) }}</span>
                                @else
                                    <span class="badge light badge-danger">{{ ucfirst($equipment->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Event Assignments</h4>
                </div>
                <div class="card-body">
                    @if($equipment->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="datatable table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Event Date</th>
                                        <th>Quantity</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipment->orders->take(10) as $order)
                                        <tr class="btn-reveal-trigger">
                                            <td class="py-2">
                                                <strong>{{ $order->order_number }}</strong>
                                            </td>
                                            <td class="py-2">
                                                {{ $order->event_date ? $order->event_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="py-2">
                                                <span class="badge badge-primary light">{{ $order->pivot->quantity }} units</span>
                                            </td>
                                            <td class="py-2 text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm" title="View Order">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($equipment->orders->count() > 10)
                            <div class="mt-3">
                                <p class="text-muted small">Showing first 10 assignments. Total: {{ $equipment->orders->count() }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No event assignments yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
