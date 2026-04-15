<div class="row">
    <div class="col-lg-6 col-md-6 mb-3">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $equipment->name ?? '') }}" required>
        <div class="invalid-feedback">
            Please enter a equipment name.
        </div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label class="form-label" for="equipment_category_id">Category</label>
        <select name="equipment_category_id" id="equipment_category_id" class="form-control default-select @error('equipment_category_id') is-invalid @enderror">
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('equipment_category_id', $equipment->equipment_category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('equipment_category_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label class="form-label" for="quantity">Total Quantity <span class="text-danger">*</span></label>
        <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', $equipment->quantity ?? '') }}" required min="0">
        <div class="invalid-feedback">
            Please enter a valid quantity (minimum 0).
        </div>
        @error('quantity')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label class="form-label" for="available_quantity">Available Quantity <span class="text-danger">*</span></label>
        <input type="number" name="available_quantity" id="available_quantity" class="form-control @error('available_quantity') is-invalid @enderror" value="{{ old('available_quantity', $equipment->available_quantity ?? '') }}" required min="0">
        <div class="invalid-feedback">
            Please enter a valid available quantity (minimum 0).
        </div>
        <small class="form-text text-muted">Available quantity cannot exceed total quantity</small>
        @error('available_quantity')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-lg-6 col-md-6 mb-3">
        <label class="form-label" for="equipment_status_id">Status <span class="text-danger">*</span></label>
        <select name="equipment_status_id" id="equipment_status_id" class="form-control default-select @error('equipment_status_id') is-invalid @enderror" required>
            <option value="">Select Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ old('equipment_status_id', $equipment->equipment_status_id ?? '') == $status->id ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback">
            Please select a status.
        </div>
        @error('equipment_status_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

