@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('page_content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Inventory Item</h4>
            </div>
            <div class="card-body">
                <div class="form-validation">
                    <form class="needs-validation" action="{{ route('inventory.update', $inventoryItem) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="name">Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    placeholder="Enter item name.." value="{{ old('name', $inventoryItem->name) }}" required>
                                <div class="invalid-feedback">
                                    Please enter an item name.
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="inventory_unit_id">Unit
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="inventory_unit_id" name="inventory_unit_id" required>
                                    <option value="">Select Unit</option>
                                    @foreach($inventoryUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ old('inventory_unit_id', $inventoryItem->inventory_unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select a unit.
                                </div>
                                @error('inventory_unit_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="current_stock">Current Stock
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="current_stock" name="current_stock" 
                                    step="0.01" min="0" value="{{ old('current_stock', $inventoryItem->current_stock) }}" required>
                                <div class="invalid-feedback">
                                    Please enter current stock.
                                </div>
                                @error('current_stock')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="minimum_stock">Minimum Stock
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="minimum_stock" name="minimum_stock" 
                                    step="0.01" min="0" value="{{ old('minimum_stock', $inventoryItem->minimum_stock) }}" required>
                                <div class="invalid-feedback">
                                    Please enter minimum stock.
                                </div>
                                @error('minimum_stock')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label" for="price_per_unit">Price Per Unit
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="price_per_unit" name="price_per_unit" 
                                    step="0.01" min="0" value="{{ old('price_per_unit', $inventoryItem->price_per_unit) }}" required>
                                <div class="invalid-feedback">
                                    Please enter price per unit.
                                </div>
                                @error('price_per_unit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                    placeholder="Enter description..">{{ old('description', $inventoryItem->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-xl-8 col-lg-10 mx-auto">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Item</button>
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
