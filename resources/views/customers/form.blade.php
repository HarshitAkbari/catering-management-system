<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $customer->name ?? '') }}" required>
        <div class="invalid-feedback">Please enter a name.</div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="mobile">Mobile <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile', $customer->mobile ?? '') }}" required>
        <div class="invalid-feedback">Please enter a mobile number.</div>
        @error('mobile')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="secondary_mobile">Secondary Mobile</label>
        <input type="text" class="form-control @error('secondary_mobile') is-invalid @enderror" id="secondary_mobile" name="secondary_mobile" value="{{ old('secondary_mobile', $customer->secondary_mobile ?? '') }}">
        <div class="invalid-feedback">Please enter a valid secondary mobile number.</div>
        @error('secondary_mobile')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label" for="email">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $customer->email ?? '') }}">
        <div class="invalid-feedback">Please enter a valid email address.</div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-12 mb-4">
        <label class="form-label" for="address">Address</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $customer->address ?? '') }}</textarea>
        @error('address')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

