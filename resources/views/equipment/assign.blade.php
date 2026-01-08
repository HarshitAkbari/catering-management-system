@extends('layout.default')

@section('title', 'Assign Equipment')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Order Details</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Assign Equipment</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Assign Equipment to Event</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-6 col-md-6">
                            <p class="mb-0"><span class="text-muted">Order :</span> <strong>{{ $order->order_number }}</strong></p>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <p class="mb-0"><span class="text-muted">Event Date :</span> <strong>{{ $order->event_date->format('M d, Y') }}</strong></p>
                        </div>
                    </div>
                    <form action="{{ route('equipment.assign.store', $order) }}" method="POST" id="assignForm">
                        @csrf
                        <div id="equipment-container">
                            @foreach($assignedEquipment as $assignedItem)
                                <div class="row mb-3 equipment-item">
                                    <div class="col-lg-6 col-md-6 mb-3">
                                        <label class="form-label">Equipment <span class="text-danger">*</span></label>
                                        <select name="equipment_ids[]" class="form-control default-select @error('equipment_ids.*') is-invalid @enderror" required>
                                            <option value="">Select Equipment</option>
                                            @foreach($equipment as $eqItem)
                                                <option value="{{ $eqItem->id }}" {{ $assignedItem->id == $eqItem->id ? 'selected' : '' }}>
                                                    {{ $eqItem->name }} (Available: {{ $eqItem->available_quantity }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('equipment_ids.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-5 col-md-5 mb-3">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="quantities[]" class="form-control @error('quantities.*') is-invalid @enderror" value="{{ $assignedItem->pivot->quantity ?? 1 }}" required min="1">
                                        @error('quantities.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-1 col-md-1 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeEquipmentRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            @if($assignedEquipment->isEmpty())
                                <div class="row mb-3 equipment-item">
                                    <div class="col-lg-6 col-md-6 mb-3">
                                        <label class="form-label">Equipment <span class="text-danger">*</span></label>
                                        <select name="equipment_ids[]" class="form-control default-select" required>
                                            <option value="">Select Equipment</option>
                                            @foreach($equipment as $eqItem)
                                                <option value="{{ $eqItem->id }}">{{ $eqItem->name }} (Available: {{ $eqItem->available_quantity }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-5 col-md-5 mb-3">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="quantities[]" class="form-control" value="1" required min="1">
                                    </div>
                                    <div class="col-lg-1 col-md-1 mb-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeEquipmentRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addEquipmentRow()">
                                    <i class="bi bi-plus-circle me-1"></i>Add Another Equipment
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Assign Equipment</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const equipmentOptions = @json($equipment->map(function($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'available' => $item->available_quantity
    ];
}));

function addEquipmentRow() {
    const container = document.getElementById('equipment-container');
    const newRow = document.createElement('div');
    newRow.className = 'row mb-3 equipment-item';
    
    let optionsHtml = '<option value="">Select Equipment</option>';
    equipmentOptions.forEach(function(item) {
        optionsHtml += `<option value="${item.id}">${item.name} (Available: ${item.available})</option>`;
    });
    
    newRow.innerHTML = `
        <div class="col-lg-6 col-md-6 mb-3">
            <label class="form-label">Equipment <span class="text-danger">*</span></label>
            <select name="equipment_ids[]" class="form-control default-select" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="col-lg-5 col-md-5 mb-3">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantities[]" class="form-control" value="1" required min="1">
        </div>
        <div class="col-lg-1 col-md-1 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeEquipmentRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    
    // Reinitialize nice select for the new dropdown
    if (typeof jQuery !== 'undefined' && jQuery('.default-select').length > 0) {
        jQuery(newRow).find('.default-select').niceSelect();
    }
}

function removeEquipmentRow(button) {
    const row = button.closest('.equipment-item');
    if (row && document.querySelectorAll('.equipment-item').length > 1) {
        row.remove();
    } else {
        alert('At least one equipment item is required.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assignForm');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endsection
