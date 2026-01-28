@extends('layout.default')

@section('title', $page_title ?? 'Equipment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="card-title mb-0">{{ $page_title ?? 'Equipment' }}</h4>
                        </div>
                        @if(isset($subtitle))
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('equipment.index') }}" class="mb-4">
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
                                <input type="text" name="name_like" id="name_filter" value="{{ $filterValues['name_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by name">
                            </div>

                            <!-- Category Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="category_filter" class="form-label">Category</label>
                                <select name="equipment_category_id" id="category_filter" class="form-control form-control-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ ($filterValues['equipment_category_id'] ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <x-filter-buttons resetRoute="{{ route('equipment.index') }}" />
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table class="datatable table table-sm mb-0 table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <x-table.sort-link field="name" label="Name" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="category" label="Category" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="quantity" label="Quantity" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="available_quantity" label="Available" />
                                    </th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipment as $item)
                                    <tr class="btn-reveal-trigger">
                                        <td class="py-2">
                                            <strong>{{ $item->name }}</strong>
                                        </td>
                                        <td class="py-2">
                                            {{ $item->equipmentCategory->name ?? '-' }}
                                        </td>
                                        <td class="py-2">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="py-2">
                                            {{ $item->available_quantity }}
                                        </td>
                                        <td class="py-2 text-end">
                                            <a href="{{ route('equipment.show', $item) }}" class="btn btn-primary btn-xs btn-view">View</a>
                                            <a href="{{ route('equipment.edit', $item) }}" class="btn btn-secondary btn-xs btn-edit">Edit</a>
                                            <x-delete-button 
                                                item-name="{{ $item->name }}"
                                                delete-url="{{ route('equipment.destroy', $item) }}"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No equipment found</p>
                                                <p class="text-muted small">Equipment will appear here once you add them</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($equipment, 'links'))
                        <div class="mt-3">
                            {{ $equipment->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<x-delete-modal id="deleteModal" />
@endsection
