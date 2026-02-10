<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name', $inventoryUnit->full_name ?? '') }}" required>
        <div class="invalid-feedback">Please enter a full name (e.g., Kilogram).</div>
    </div>

    <div class="col-md-6 mb-4">
        <label class="form-label" for="short_name">Short Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('short_name') is-invalid @enderror" id="short_name" name="short_name" value="{{ old('short_name', $inventoryUnit->short_name ?? '') }}" required maxlength="10">
        <div class="invalid-feedback">Please enter a short name (e.g., kg).</div>
    </div>
</div>

