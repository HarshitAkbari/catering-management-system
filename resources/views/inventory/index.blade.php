@extends('layouts.app')

@section('title', $page_title ?? 'Inventory')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Inventory' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                    <x-add-button module="inventory" route="inventory.create" label="Add Inventory Item" />
                </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('inventory.index') }}" class="mb-4">
                    <!-- Preserve sort parameters -->
                    @if(request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if(request('sort_order'))
                        <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                    @endif
                    
                    <div class="row g-2 align-items-end mb-3">
                        <!-- Name Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="name_filter" class="form-label">Name</label>
                            <input type="text" name="name_like" id="name_filter" value="{{ $filterValues['name_like'] ?? '' }}" class="form-control form-control-sm">
                        </div>

                        <!-- Unit Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="unit_filter" class="form-label">Unit</label>
                            <select name="inventory_unit_id" id="unit_filter" class="form-control form-control-sm">
                                <option value="">All Units</option>
                                @foreach($inventoryUnits ?? [] as $unit)
                                    <option value="{{ $unit->id }}" {{ ($filterValues['inventory_unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stock Status Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="stock_status_filter" class="form-label">Stock Status</label>
                            <select name="stock_status" id="stock_status_filter" class="form-control form-control-sm">
                                <option value="">All</option>
                                <option value="low" {{ ($filterValues['stock_status'] ?? '') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <x-filter-buttons resetRoute="{{ route('inventory.index') }}" />
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <x-table.sort-link field="name" label="Name" />
                                </th>
                                <th>
                                    <x-table.sort-link field="unit" label="Unit" />
                                </th>
                                <th>
                                    <x-table.sort-link field="current_stock" label="Current Stock" />
                                </th>
                                <th>
                                    <x-table.sort-link field="minimum_stock" label="Minimum Stock" />
                                </th>
                                <th>
                                    <x-table.sort-link field="price_per_unit" label="Price/Unit" />
                                </th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->inventoryUnit->name ?? '-' }}</td>
                                    <td>{{ number_format($item->current_stock, 2) }}</td>
                                    <td>{{ number_format($item->minimum_stock, 2) }}</td>
                                    <td>₹{{ number_format($item->price_per_unit, 2) }}</td>
                                    <td>
                                        @if($item->isLowStock())
                                            <span class="badge badge-danger">Low Stock</span>
                                        @else
                                            <span class="badge badge-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.show', $item) }}" class="btn btn-primary btn-xs btn-view">View</a>
                                        <x-edit-button module="inventory" route="inventory.edit" :model="$item" />
                                        <x-delete-button 
                                            module="inventory"
                                            item-name="{{ $item->name }}"
                                            delete-url="{{ route('inventory.destroy', $item) }}"
                                        />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <p class="text-muted">No inventory items found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($inventoryItems, 'links'))
                    <div class="mt-3">
                        {{ $inventoryItems->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-delete-modal id="deleteModal" />
@endsection
