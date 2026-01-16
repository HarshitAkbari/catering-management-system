@extends('layouts.app')

@section('title', 'Low Stock Alerts')

@section('page_content')
<div class="row page-titles">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">Low Stock Alerts</a></li>
    </ol>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Low Stock Alerts</h4>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-2"></i>Back to Inventory
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Current Stock</th>
                                <th>Minimum Stock</th>
                                <th>Deficit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td><strong class="text-danger">{{ number_format($item->current_stock, 2) }}</strong></td>
                                    <td>{{ number_format($item->minimum_stock, 2) }}</td>
                                    <td><strong class="text-danger">{{ number_format($item->minimum_stock - $item->current_stock, 2) }} {{ $item->unit }}</strong></td>
                                    <td>
                                        <a href="{{ route('inventory.stock-in') }}?item={{ $item->id }}" class="btn btn-success btn-sm">
                                            <i class="bi bi-box-arrow-in-down me-2"></i>Add Stock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-muted mb-1">No low stock items</p>
                                            <p class="text-muted small">All items are well stocked!</p>
                                        </div>
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
