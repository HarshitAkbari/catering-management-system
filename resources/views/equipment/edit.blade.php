@extends('layout.default')

@section('title', 'Edit Equipment')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('equipment.index') }}">Equipment</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Equipment</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Equipment</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>There were errors with your submission:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('equipment.update', $equipment) }}" method="POST" id="equipmentForm" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $equipment->name) }}" placeholder="Enter equipment name.." required>
                                    <div class="invalid-feedback">
                                        Please enter a equipment name.
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <label class="form-label" for="category">Category</label>
                                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $equipment->category) }}" placeholder="e.g., Tables, Chairs">
                                    @error('category')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <label class="form-label" for="quantity">Total Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $equipment->quantity) }}" placeholder="Enter total quantity.." required min="0">
                                    <div class="invalid-feedback">
                                        Please enter a valid quantity (minimum 0).
                                    </div>
                                    @error('quantity')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <label class="form-label" for="available_quantity">Available Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="available_quantity" id="available_quantity" class="form-control @error('available_quantity') is-invalid @enderror" value="{{ old('available_quantity', $equipment->available_quantity) }}" placeholder="Enter available quantity.." required min="0">
                                    <div class="invalid-feedback">
                                        Please enter a valid available quantity (minimum 0).
                                    </div>
                                    <small class="form-text text-muted">Available quantity cannot exceed total quantity</small>
                                    @error('available_quantity')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control default-select @error('status') is-invalid @enderror" required>
                                        <option value="">Select Status</option>
                                        <option value="available" {{ old('status', $equipment->status) === 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="damaged" {{ old('status', $equipment->status) === 'damaged' ? 'selected' : '' }}>Damaged</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a status.
                                    </div>
                                    @error('status')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Equipment</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('equipmentForm');
        const quantityInput = document.getElementById('quantity');
        const availableQuantityInput = document.getElementById('available_quantity');
        
        function validateQuantities() {
            const quantity = parseInt(quantityInput.value) || 0;
            const availableQuantity = parseInt(availableQuantityInput.value) || 0;
            
            if (availableQuantity > quantity) {
                availableQuantityInput.setCustomValidity('Available quantity cannot exceed total quantity');
                availableQuantityInput.classList.add('is-invalid');
            } else {
                availableQuantityInput.setCustomValidity('');
                if (form.classList.contains('was-validated')) {
                    availableQuantityInput.classList.remove('is-invalid');
                }
            }
        }
        
        quantityInput.addEventListener('input', validateQuantities);
        availableQuantityInput.addEventListener('input', validateQuantities);
    });
</script>
@endsection
@endsection
