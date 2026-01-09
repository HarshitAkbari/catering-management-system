@extends('layouts.app')

@section('title', 'Vendors')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Vendors</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Vendors</h4>
                    <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Vendor
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-sm mb-0 table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact Person</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th></th>
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
                                            <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-primary btn-sm me-1" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-info btn-sm me-1" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" title="Delete" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteVendorModal"
                                                data-vendor-id="{{ $vendor->id }}"
                                                data-vendor-name="{{ $vendor->name }}"
                                                data-vendor-url="{{ route('vendors.destroy', $vendor) }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteVendorModal" tabindex="-1" aria-labelledby="deleteVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteVendorModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="vendorNameToDelete"></strong>?</p>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteVendorForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Vendor</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteVendorModal');
        const deleteForm = document.getElementById('deleteVendorForm');
        const vendorNameElement = document.getElementById('vendorNameToDelete');
        
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                
                // Extract info from data-* attributes
                const vendorId = button.getAttribute('data-vendor-id');
                const vendorName = button.getAttribute('data-vendor-name');
                const vendorUrl = button.getAttribute('data-vendor-url');
                
                // Update modal content
                vendorNameElement.textContent = vendorName;
                
                // Update form action
                deleteForm.action = vendorUrl;
            });
        }
    });
</script>
@endsection
