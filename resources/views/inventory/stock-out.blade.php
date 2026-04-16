@extends('layouts.app')

@section('title', 'Stock Out')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            @include('components.flash-messages')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stock Out</h4>
                </div>
                <div class="card-body">
                    {{-- Tips Section --}}
                    <x-tips-section>
                        <x-tip-item>
                            Select the inventory item you want to reduce stock for from the dropdown list
                        </x-tip-item>
                        
                        <x-tip-item>
                            Enter the quantity being used or removed. The system will validate that sufficient stock is available
                        </x-tip-item>
                        
                        <x-tip-item>
                            Use stock out to record items used in orders, damaged items, expired items, or any other stock reduction
                        </x-tip-item>
                        
                        <x-tip-item>
                            Add notes to document the reason for stock out, order number, or any other relevant information
                        </x-tip-item>
                        
                        <x-tip-item>
                            Stock out transactions are automatically recorded and will update the current stock level of the selected item
                        </x-tip-item>
                        
                        <x-tip-item>
                            If stock goes below the minimum threshold after stock out, you'll receive a low stock alert
                        </x-tip-item>
                    </x-tips-section>
                    
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('inventory.stock-out.store') }}" method="POST" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="inventory_item_id">Inventory Item
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="inventory_item_id" id="inventory_item_id" required class="form-control default-select @error('inventory_item_id') is-invalid @enderror">
                                        <option value="">Select Item</option>
                                        @foreach($inventoryItems as $item)
                                            <option value="{{ $item->id }}" data-stock="{{ $item->current_stock }}" {{ old('inventory_item_id') == $item->id ? 'selected' : '' }}>
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
</div>
@endsection
