@extends('layout.default')

@section('content')
	<div class="container-fluid">
        <!-- row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create New Order</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-alt alert-danger solid alert-dismissible fade show" role="alert">
                                <strong>There were errors with your submission:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="form-validation">
                            <form class="needs-validation" action="{{ route('orders.store') }}" method="POST" novalidate>
                                @csrf

                                <div class="row">
                                        <div class="row">
                                            <!-- First Row: 3 Columns -->
                                            <div class="col-md-4 mb-4">
                                                <label class="form-label" for="customer_name">Customer Name
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                                    placeholder="Enter customer name.." value="{{ old('customer_name') }}" required>
                                                <div class="invalid-feedback">
                                                    Please enter a customer name.
                                                </div>
                                                @error('customer_name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <label class="form-label" for="customer_email">Customer Email
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                                    placeholder="Enter customer email.." value="{{ old('customer_email') }}" required>
                                                <div class="invalid-feedback">
                                                    Please enter a valid email.
                                                </div>
                                                @error('customer_email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <label class="form-label" for="customer_mobile">Contact Number
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="customer_mobile" name="customer_mobile" 
                                                    placeholder="Enter contact number.." value="{{ old('customer_mobile') }}" required>
                                                <div class="invalid-feedback">
                                                    Please enter a contact number.
                                                </div>
                                                @error('customer_mobile')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Second Row: Full Width Address -->
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <label class="form-label" for="address">Address
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="address" name="address" rows="3" 
                                                    placeholder="Enter address.." required>{{ old('address') }}</textarea>
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
                                            <button type="button" id="add-event-btn" class="btn btn-success">
                                                <i class="bi bi-plus-circle me-2"></i>Add Event
                                            </button>
                                        </div>
                                </div>

                                <!-- Events Table -->
                                <div class="row">
                                        <div id="events-container" class="mt-4 d-none">
                                            <h5 class="mb-3">Events</h5>
                                            @error('events')
                                                <div class="alert alert-alt alert-danger solid">{{ $message }}</div>
                                            @enderror
                                            @error('events.*')
                                                <div class="alert alert-alt alert-danger solid">{{ $message }}</div>
                                            @enderror
                                            <div class="table-responsive">
                                                <table class="datatable-simple table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Event Date</th>
                                                            <th>Event Time</th>
                                                            <th>Event Menu</th>
                                                            <th>Guest Count</th>
                                                            <th>Order Type</th>
                                                            <th>Dish Price</th>
                                                            <th>Cost</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="events-table-body">
                                                        <!-- Events will be added here dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                </div>

                                <!-- Hidden input for events data -->
                                <input type="hidden" name="events" id="events-data" value="">

                                <div class="row mt-4">
                                    <div class="col-xl-8 col-lg-10 mx-auto">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" id="submit-btn" class="btn btn-primary">Create Order</button>
                                        </div>
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

<!-- Event Modal -->
<div class="modal fade" id="event-modal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Add Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="event-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal-event-date" class="form-label">Event Date <span class="text-danger">*</span></label>
                            <input type="date" id="modal-event-date" required class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal-event-time" class="form-label">Event Time <span class="text-danger">*</span></label>
                            <select id="modal-event-time" required class="form-control default-select">
                                <option value="">Select Time</option>
                                <option value="morning">Morning</option>
                                <option value="afternoon">Afternoon</option>
                                <option value="evening">Evening</option>
                                <option value="night_snack">Snack</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="modal-event-menu" class="form-label">Event Menu <span class="text-danger">*</span></label>
                            <input type="text" id="modal-event-menu" required class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal-guest-count" class="form-label">Guest Count <span class="text-danger">*</span></label>
                            <input type="number" id="modal-guest-count" min="1" required class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal-order-type" class="form-label">Order Type</label>
                            <select id="modal-order-type" class="form-control default-select">
                                <option value="">Select Order Type</option>
                                <option value="full_service">Full Service</option>
                                <option value="preparation_only">Preparation Only</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal-dish-price" class="form-label">Dish Price <span class="text-danger">*</span></label>
                            <input type="number" id="modal-dish-price" step="0.01" min="0" required class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal-cost" class="form-label">Cost</label>
                            <input type="text" id="modal-cost" readonly class="form-control" value="0.00">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="save-event-btn" class="btn btn-primary">Add Event</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        (function () {
          'use strict'

          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.querySelectorAll('.needs-validation')

          // Loop over them and prevent submission
          Array.prototype.slice.call(forms)
            .forEach(function (form) {
              form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                  event.preventDefault()
                  event.stopPropagation()
                }

                form.classList.add('was-validated')
              }, false)
            })
        })()

        let events = [];
        let editingIndex = -1;
        let eventModal;

        document.addEventListener('DOMContentLoaded', function() {
            eventModal = new bootstrap.Modal(document.getElementById('event-modal'));

            // Open modal when Add Event button is clicked
            document.getElementById('add-event-btn').addEventListener('click', function() {
                editingIndex = -1;
                resetEventForm();
                eventModal.show();
            });

            // Calculate cost when guest count or dish price changes
            document.getElementById('modal-guest-count').addEventListener('input', calculateCost);
            document.getElementById('modal-dish-price').addEventListener('input', calculateCost);

            // Save event
            document.getElementById('save-event-btn').addEventListener('click', function() {
                const form = document.getElementById('event-form');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const eventData = {
                    event_date: document.getElementById('modal-event-date').value,
                    event_time: document.getElementById('modal-event-time').value,
                    event_menu: document.getElementById('modal-event-menu').value,
                    guest_count: parseInt(document.getElementById('modal-guest-count').value),
                    order_type: document.getElementById('modal-order-type').value || null,
                    dish_price: parseFloat(document.getElementById('modal-dish-price').value),
                    cost: parseFloat(document.getElementById('modal-cost').value)
                };

                if (editingIndex >= 0) {
                    events[editingIndex] = eventData;
                } else {
                    events.push(eventData);
                }

                updateEventsTable();
                updateEventsData();
                eventModal.hide();
                resetEventForm();
            });

            // Validate events before form submission
            document.querySelector('form[action="{{ route('orders.store') }}"]').addEventListener('submit', function(e) {
                updateEventsData();
                
                if (events.length === 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        });

        function calculateCost() {
            const guestCount = parseFloat(document.getElementById('modal-guest-count').value) || 0;
            const dishPrice = parseFloat(document.getElementById('modal-dish-price').value) || 0;
            const cost = guestCount * dishPrice;
            document.getElementById('modal-cost').value = cost.toFixed(2);
        }

        function resetEventForm() {
            document.getElementById('event-form').reset();
            document.getElementById('modal-cost').value = '0.00';
            document.getElementById('save-event-btn').textContent = 'Add Event';
        }

        function updateEventsTable() {
            const tbody = document.getElementById('events-table-body');
            const container = document.getElementById('events-container');
            
            if (events.length === 0) {
                container.classList.add('d-none');
                tbody.innerHTML = '';
                return;
            }

            container.classList.remove('d-none');
            tbody.innerHTML = events.map((event, index) => {
                const eventTimeLabels = {
                    'morning': 'Morning',
                    'afternoon': 'Afternoon',
                    'evening': 'Evening',
                    'night_snack': 'Snack'
                };

                const orderTypeLabels = {
                    'full_service': 'Full Service',
                    'preparation_only': 'Preparation Only'
                };

                return `
                    <tr>
                        <td>${event.event_date}</td>
                        <td>${eventTimeLabels[event.event_time] || event.event_time}</td>
                        <td>${event.event_menu}</td>
                        <td>${event.guest_count}</td>
                        <td>${orderTypeLabels[event.order_type] || event.order_type || '-'}</td>
                        <td>₹${event.dish_price.toFixed(2)}</td>
                        <td><strong>₹${event.cost.toFixed(2)}</strong></td>
                        <td>
                            <button type="button" onclick="editEvent(${index})" class="btn btn-sm btn-primary me-1">Edit</button>
                            <button type="button" onclick="deleteEvent(${index})" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function editEvent(index) {
            editingIndex = index;
            const event = events[index];
            
            document.getElementById('modal-event-date').value = event.event_date;
            document.getElementById('modal-event-time').value = event.event_time;
            document.getElementById('modal-event-menu').value = event.event_menu;
            document.getElementById('modal-guest-count').value = event.guest_count;
            document.getElementById('modal-order-type').value = event.order_type || '';
            document.getElementById('modal-dish-price').value = event.dish_price;
            document.getElementById('modal-cost').value = event.cost.toFixed(2);
            document.getElementById('save-event-btn').textContent = 'Update Event';
            
            eventModal.show();
        }

        function deleteEvent(index) {
            if (confirm('Are you sure you want to delete this event?')) {
                events.splice(index, 1);
                updateEventsTable();
                updateEventsData();
            }
        }

        function updateEventsData() {
            document.getElementById('events-data').value = JSON.stringify(events);
        }
    </script>
@endsection
