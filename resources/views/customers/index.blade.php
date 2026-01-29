@extends('layouts.app')

@section('title', $page_title ?? 'Customers')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="card-title mb-0">{{ $page_title ?? 'Customers' }}</h4>
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
                <form method="GET" action="{{ route('customers.index') }}" class="mb-4">
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

                        <!-- Email Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="email_filter" class="form-label">Email</label>
                            <input type="text" name="email_like" id="email_filter" value="{{ $filterValues['email_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by email">
                        </div>

                        <!-- Mobile Filter -->
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                            <label for="mobile_filter" class="form-label">Mobile</label>
                            <input type="text" name="mobile_like" id="mobile_filter" value="{{ $filterValues['mobile_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by mobile">
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <x-filter-buttons resetRoute="{{ route('customers.index') }}" />
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="datatable table table-sm mb-0 table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <x-table.sort-link field="name" label="Customer" />
                                </th>
                                <th>
                                    <x-table.sort-link field="mobile" label="Mobile" />
                                </th>
                                <th>
                                    <x-table.sort-link field="email" label="Email" />
                                </th>
                                <th>Total Orders</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                            <tbody id="customers">
                                @forelse($customers as $customer)
                                    <tr class="btn-reveal-trigger">
                                        <td class="py-3">
                                            <a href="{{ route('customers.show', $customer) }}">
                                                <div class="media d-flex align-items-center">
                                                    <div class="avatar avatar-xl me-2">
                                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
                                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h5 class="mb-0 fs--1">{{ $customer->name }}</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="py-2">
                                            <a href="tel:{{ $customer->mobile }}">{{ $customer->mobile }}</a>
                                        </td>
                                        <td class="py-2">
                                            @if($customer->email)
                                                <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            <span class="badge badge-primary light">
                                                {{ $customer->orders_count }} {{ Str::plural('order', $customer->orders_count) }}
                                            </span>
                                        </td>
                                        <td class="py-2">
                                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-primary btn-xs" title="View">
                                                View
                                            </a>
                                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-xs" title="Edit">
                                                    Edit
                                                </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No customers found</p>
                                                <p class="text-muted small">Customers will appear here once orders are created</p>
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
</div>
@endsection
