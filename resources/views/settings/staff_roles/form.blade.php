<div class="row">
    <div class="col-md-12 mb-4">
        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $staffRole->name ?? '') }}" required>
        <div class="invalid-feedback">Please enter a name.</div>
        @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-12 mb-4">
        <label class="form-label" for="description">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $staffRole->description ?? '') }}</textarea>
        @error('description')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

