<div class="row">
    <div class="col-md-12 mb-4">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $equipmentCategory->name ?? '') }}" required>
        <div class="invalid-feedback">Please enter a name.</div>
    </div>
</div>

