@extends('layouts.app')

@section('title', 'Inventory Item Details')

@section('page_content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $inventoryItem->name }}</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.index') }}">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Name :</span> <strong>{{ $inventoryItem->name }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Unit :</span> <strong>{{ $inventoryItem->unit }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Current Stock :</span> <strong>{{ number_format($inventoryItem->current_stock, 2) }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Minimum Stock :</span> <strong>{{ number_format($inventoryItem->minimum_stock, 2) }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Price Per Unit :</span> <strong>₹{{ number_format($inventoryItem->price_per_unit, 2) }}</strong></p>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <p class="mb-0"><span class="text-muted">Status :</span> 
                            @if($inventoryItem->isLowStock())
                                <span class="badge badge-danger">Low Stock</span>
                            @else
                                <span class="badge badge-success">In Stock</span>
                            @endif
                        </p>
                    </div>
                    @if($inventoryItem->description)
                    <div class="col-lg-12 mb-3">
                        <p class="mb-0"><span class="text-muted">Description :</span> <strong>{{ $inventoryItem->description }}</strong></p>
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
                <div class="table-responsive">
                    <table class="table table-sm mb-0 table-striped">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Vendor</th>
                                <th>Date/Time</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryItem->stockTransactions->take(10) as $transaction)
                                <tr>
                                    <td>
                                        <span class="badge {{ $transaction->type === 'in' ? 'badge-success' : 'badge-danger' }}">
                                            {{ strtoupper($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($transaction->quantity, 2) }} {{ $inventoryItem->unit }}</td>
                                    <td>
                                        @if($transaction->price)
                                            ₹{{ number_format($transaction->price, 2) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->vendor)
                                            {{ $transaction->vendor->name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($transaction->notes)
                                            {{ $transaction->notes }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted">No transactions yet</p>
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
