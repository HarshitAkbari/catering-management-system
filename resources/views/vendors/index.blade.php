@extends('layouts.app')

@section('title', $page_title ?? 'Vendors')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="card-title mb-0">{{ $page_title ?? 'Vendors' }}</h4>
                            </div>
                            @if(isset($subtitle))
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <h6 class="text-muted mb-0">{{ $subtitle }}</h6>
                                </div>
                            @endif
                        </div>
                        <div>
                            <x-add-button module="vendors" route="vendors.create" label="Add Vendor" class="btn btn-primary btn-sm" />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('vendors.index') }}" class="mb-4">
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

                            <!-- Contact Person Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="contact_person_filter" class="form-label">Contact Person</label>
                                <input type="text" name="contact_person_like" id="contact_person_filter" value="{{ $filterValues['contact_person_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by contact person">
                            </div>

                            <!-- Email Filter -->
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <label for="email_filter" class="form-label">Email</label>
                                <input type="text" name="email_like" id="email_filter" value="{{ $filterValues['email_like'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by email">
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <x-filter-buttons resetRoute="{{ route('vendors.index') }}" />
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
                                        <x-table.sort-link field="contact_person" label="Contact Person" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="phone" label="Phone" />
                                    </th>
                                    <th>
                                        <x-table.sort-link field="email" label="Email" />
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="vendors">
                                @forelse($vendors as $vendor)
                                    <tr class="btn-reveal-trigger">
                                        <td class="py-3">
                                            <a href="{{ route('vendors.show', $vendor) }}">
                                                <div class="media d-flex align-items-center">
                                                    <div class="avatar avatar-xl me-2">
                                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 30px; height: 30px; font-size: 14px;">
                                                            {{ strtoupper(substr($vendor->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <h5 class="mb-0 fs--1">{{ $vendor->name }}</h5>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="py-2">
                                            {{ $vendor->contact_person ?? 'N/A' }}
                                        </td>
                                        <td class="py-2">
                                            <a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a>
                                        </td>
                                        <td class="py-2">
                                            @if($vendor->email)
                                                <a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="py-2 text-end">
                                            <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-primary btn-xs btn-view">View</a>
                                            <x-edit-button module="vendors" route="vendors.edit" :model="$vendor" />
                                            <x-delete-button 
                                                module="vendors"
                                                item-name="{{ $vendor->name }}"
                                                delete-url="{{ route('vendors.destroy', $vendor) }}"
                                                modal-id="deleteVendorModal"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <svg class="mb-3" width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #9ca3af;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                <p class="text-muted mb-1">No vendors found</p>
                                                <p class="text-muted small">Vendors will appear here once they are created</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($vendors, 'links'))
                        <div class="mt-3">
                            {{ $vendors->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<x-delete-modal 
    id="deleteVendorModal" 
    title="Confirm Deletion"
    delete-button-text="Delete Vendor"
/>
@endsection
