<div class="row">
    <div class="row">
        <!-- First Row: 3 Columns -->
        <div class="col-md-3 mb-4">
            <label class="form-label" for="customer_name">Customer Name
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                value="{{ old('customer_name', isset($order) && $order->customer ? $order->customer->name : '') }}" required>
            <div class="invalid-feedback">
                Please enter a customer name.
            </div>
            @error('customer_name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3 mb-4">
            <label class="form-label" for="customer_email">Customer Email
                <span class="text-danger">*</span>
            </label>
            <input type="email" class="form-control" id="customer_email" name="customer_email" 
                value="{{ old('customer_email', isset($order) && $order->customer ? ($order->customer->email ?? '') : '') }}" required>
            <div class="invalid-feedback">
                Please enter a valid email.
            </div>
            @error('customer_email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3 mb-4">
            <label class="form-label" for="customer_mobile">Contact Number
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" 
                value="{{ old('customer_mobile', isset($order) && $order->customer ? $order->customer->mobile : '') }}" required>
            <div class="invalid-feedback">
                Please enter a contact number.
            </div>
            @error('customer_mobile')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3 mb-4">
            <label class="form-label" for="customer_secondary_mobile">Secondary Contact Number</label>
            <input type="text" class="form-control" id="customer_secondary_mobile" name="customer_secondary_mobile" 
                value="{{ old('customer_secondary_mobile', isset($order) && $order->customer ? ($order->customer->secondary_mobile ?? '') : '') }}">
            <div class="invalid-feedback">
                Please enter a valid secondary contact number.
            </div>
            @error('customer_secondary_mobile')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <!-- Third Row: Full Width Address -->
    <div class="row">
        <div class="col-12 mb-4">
            <label class="form-label" for="address">Address
                <span class="text-danger">*</span>
            </label>
            <textarea class="form-control" id="address" name="address" rows="3" 
                required>{{ old('address', isset($order) ? $order->address : '') }}</textarea>
            <div class="invalid-feedback">
                Please enter an address.
            </div>
            @error('address')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <!-- Add Event Button -->
    <div class="mb-4">
        <button type="button" id="add-event-btn" class="btn btn-success ">
            <i class="bi bi-plus-circle me-2"></i>Add Event
        </button>
    </div>
</div>

