@extends('layouts.app')

@section('title', 'Assign Staff to Event')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Assign Staff to Event</h4>
            </div>
            <div class="card-body">
                <!-- Event Information -->
                <div class="alert alert-info">
                    <h5>Event Details</h5>
                    <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                    <p class="mb-1"><strong>Customer:</strong> {{ $order->customer->name }}</p>
                    <p class="mb-1"><strong>Event Date:</strong> {{ $order->event_date->format('M d, Y') }}</p>
                    <p class="mb-0"><strong>Event Time:</strong> {{ $order->eventTime->name ?? '-' }}</p>
                </div>

                @include('error.alerts')
                <form action="{{ route('staff.assign.store', $order) }}" method="POST" id="assignStaffForm">
                    @csrf
                    
                    <h5 class="mb-3">Available Staff</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                                    </th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Event Role</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($availableStaff as $staff)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}" class="staff-checkbox" onchange="toggleRoleInput(this)">
                                        </td>
                                        <td>{{ $staff->name }}</td>
                                        <td>{{ $staff->phone }}</td>
                                        <td><span class="badge badge-info light">{{ $staff->staff_role }}</span></td>
                                        <td>
                                            <input type="text" name="roles[]" class="form-control form-control-sm role-input" placeholder="Role for event" disabled>
                                        </td>
                                        <td>
                                            <input type="text" name="notes[]" class="form-control form-control-sm notes-input" placeholder="Notes" disabled>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No available staff for this event date/time</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($assignedStaff->count() > 0)
                        <hr>
                        <h5 class="mb-3">Currently Assigned Staff</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedStaff as $staff)
                                        <tr>
                                            <td>{{ $staff->name }}</td>
                                            <td><span class="badge badge-info light">{{ $staff->pivot->role ?? $staff->staff_role }}</span></td>
                                            <td>{{ $staff->pivot->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Assign Staff</button>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    const roleInputs = document.querySelectorAll('.role-input');
    const notesInputs = document.querySelectorAll('.notes-input');
    
    checkboxes.forEach((cb, index) => {
        cb.checked = checkbox.checked;
        roleInputs[index].disabled = !checkbox.checked;
        notesInputs[index].disabled = !checkbox.checked;
    });
}

function toggleRoleInput(checkbox) {
    const row = checkbox.closest('tr');
    const roleInput = row.querySelector('.role-input');
    const notesInput = row.querySelector('.notes-input');
    
    roleInput.disabled = !checkbox.checked;
    notesInput.disabled = !checkbox.checked;
    
    if (!checkbox.checked) {
        roleInput.value = '';
        notesInput.value = '';
    }
}

document.getElementById('assignStaffForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.staff-checkbox:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert('Please select at least one staff member.');
        return false;
    }
    
    // Ensure role is filled for each selected staff
    checked.forEach((cb) => {
        const row = cb.closest('tr');
        const roleInput = row.querySelector('.role-input');
        if (!roleInput.value.trim()) {
            e.preventDefault();
            alert('Please specify a role for each selected staff member.');
            roleInput.focus();
            return false;
        }
    });
});
</script>
@endsection

