<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
            value="{{ old('name', $inventoryItem->name ?? '') }}" required>
        <div class="invalid-feedback">Please enter an item name.</div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="inventory_unit_id">Unit <span class="text-danger">*</span></label>
        <select class="form-control @error('inventory_unit_id') is-invalid @enderror" id="inventory_unit_id" name="inventory_unit_id" required>
            <option value="">Select Unit</option>
            @foreach($inventoryUnits as $unit)
                <option value="{{ $unit->id }}" {{ old('inventory_unit_id', $inventoryItem->inventory_unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">Please select a unit.</div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="current_stock">Current Stock <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('current_stock') is-invalid @enderror" id="current_stock" name="current_stock" 
            step="0.01" min="0" value="{{ old('current_stock', $inventoryItem->current_stock ?? '') }}" required>
        <div class="invalid-feedback">Please enter current stock.</div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="minimum_stock">Minimum Stock <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" id="minimum_stock" name="minimum_stock" 
            step="0.01" min="0" value="{{ old('minimum_stock', $inventoryItem->minimum_stock ?? '') }}" required>
        <div class="invalid-feedback">Please enter minimum stock.</div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="price_per_unit">Price Per Unit <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('price_per_unit') is-invalid @enderror" id="price_per_unit" name="price_per_unit" 
            step="0.01" min="0" value="{{ old('price_per_unit', $inventoryItem->price_per_unit ?? '') }}" required>
        <div class="invalid-feedback">Please enter price per unit.</div>
    </div>

    <div class="col-md-12 mb-4">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $inventoryItem->description ?? '') }}</textarea>
    </div>
</div>

