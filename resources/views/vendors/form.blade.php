<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="name">Vendor Name
            <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
            value="{{ old('name', $vendor->name ?? '') }}" required>
        <div class="invalid-feedback">
            Please enter a vendor name.
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="contact_person">Contact Person</label>
        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" 
            value="{{ old('contact_person', $vendor->contact_person ?? '') }}">
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="phone">Phone
            <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" 
            value="{{ old('phone', $vendor->phone ?? '') }}" required>
        <div class="invalid-feedback">
            Please enter a phone number.
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="email">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
            value="{{ old('email', $vendor->email ?? '') }}">
        <div class="invalid-feedback">
            Please enter a valid email address.
        </div>
    </div>

    <div class="col-12 mb-4">
        <label class="form-label" for="address">Address</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $vendor->address ?? '') }}</textarea>
    </div>
</div>

