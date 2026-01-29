@extends('layouts.app')

@section('title', 'Stock In')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            @include('components.flash-messages')
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
                                    <select name="inventory_item_id" id="inventory_item_id" required class="form-control default-select @error('inventory_item_id') is-invalid @enderror">
                                        <option value="">Select Item</option>
                                        @foreach($inventoryItems as $item)
                                            <option value="{{ $item->id }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('inventory_item_id')
                                            {{ $message }}
                                        @else
                                            Please select an inventory item.
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="quantity">Quantity
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" 
                                        step="0.01" min="0.01" value="{{ old('quantity') }}" required>
                                    <div class="invalid-feedback">
                                        @error('quantity')
                                            {{ $message }}
                                        @else
                                            Please enter a quantity.
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="price">Price (Optional)</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" 
                                        step="0.01" min="0" value="{{ old('price') }}">
                                    <div class="invalid-feedback">
                                        @error('price')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="vendor_id">Vendor (Optional)</label>
                                    <select name="vendor_id" id="vendor_id" class="form-control default-select @error('vendor_id') is-invalid @enderror">
                                        <option value="">Select Vendor</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('vendor_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label" for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    <div class="invalid-feedback">
                                        @error('notes')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tips Section --}}
    <x-tips-section>
        <x-tip-item>
            Select the inventory item you want to add stock for from the dropdown list
        </x-tip-item>
        
        <x-tip-item>
            Enter the quantity being added. Use decimal values for items measured in units like kg, liters, etc.
        </x-tip-item>
        
        <x-tip-item>
            Optionally record the purchase price and vendor for better inventory tracking and cost management
        </x-tip-item>
        
        <x-tip-item>
            Add notes to document the reason for stock in, purchase order number, or any other relevant information
        </x-tip-item>
        
        <x-tip-item>
            Stock in transactions are automatically recorded and will update the current stock level of the selected item
        </x-tip-item>
    </x-tips-section>
</div>
@endsection
