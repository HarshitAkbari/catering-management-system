@extends('layouts.app')

@section('title', 'Stock In')

@section('page_content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Stock In</h4>
            </div>
            <div class="card-body">
                <div class="form-validation">
                    <form class="needs-validation" action="{{ route('inventory.stock-in.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="inventory_item_id">Inventory Item
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="inventory_item_id" id="inventory_item_id" required class="form-control default-select">
                                    <option value="">Select Item</option>
                                    @foreach($inventoryItems as $item)
                                        <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} ({{ $item->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select an inventory item.
                                </div>
                                @error('inventory_item_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="quantity">Quantity
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                    step="0.01" min="0.01" value="{{ old('quantity') }}" required>
                                <div class="invalid-feedback">
                                    Please enter a quantity.
                                </div>
                                @error('quantity')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="price">Price (Optional)</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                    step="0.01" min="0" value="{{ old('price') }}">
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="vendor_id">Vendor (Optional)</label>
                                <select name="vendor_id" id="vendor_id" class="form-control default-select">
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                    placeholder="Enter notes..">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-xl-8 col-lg-10 mx-auto">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-box-arrow-in-down me-2"></i>Add Stock
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
</script>
@endsection
