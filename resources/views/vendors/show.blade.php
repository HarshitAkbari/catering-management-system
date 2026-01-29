@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Vendor Information</h4>
                    <div class="mt-3">
                        <a href="{{ route('vendors.index') }}">
                            <i class="bi bi-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <p class="mb-0"><strong>Name :</strong> <span class="text-muted">{{ $vendor->name }}</span></p>
                        </div>
                        @if($vendor->contact_person)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <p class="mb-0"><strong>Contact Person :</strong> <span class="text-muted">{{ $vendor->contact_person }}</span></p>
                            </div>
                        @endif
                        <div class="col-lg-4 col-md-6 mb-3">
                            <p class="mb-0"><strong>Phone :</strong> <span class="text-muted"><a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a></span></p>
                        </div>
                        @if($vendor->email)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <p class="mb-0"><strong>Email :</strong> <span class="text-muted"><a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a></span></p>
                            </div>
                        @endif
                        @if($vendor->address)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <p class="mb-0"><strong>Address :</strong> <span class="text-muted">{{ $vendor->address }}</span></p>
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
                    <h4 class="card-title">Recent Transactions</h4>
                </div>
                <div class="card-body">
                    @if($vendor->stockTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="datatable table table-sm mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Date/Time</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendor->stockTransactions->take(10) as $transaction)
                                        <tr class="btn-reveal-trigger">
                                            <td class="py-2">
                                                <strong>{{ $transaction->inventoryItem->name }}</strong>
                                            </td>
                                            <td class="py-2">
                                                @if($transaction->type === 'in')
                                                    <span class="badge badge-success">IN</span>
                                                @else
                                                    <span class="badge badge-danger">OUT</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                {{ number_format($transaction->quantity, 2) }} {{ $transaction->inventoryItem->unit }}
                                            </td>
                                            <td class="py-2">
                                                @if($transaction->price)
                                                    <strong>₹{{ number_format($transaction->price, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                {{ $transaction->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="py-2">
                                                @if($transaction->notes)
                                                    {{ $transaction->notes }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No transactions found for this vendor</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
