@extends('layouts.app')

@section('title', $page_title ?? 'Edit Order')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit {{ $page_title ?? 'Order' }}</h4>
                    <div class="card-tools">
                        <a href="{{ route('orders.index') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('orders.update', $order) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            @include('orders.form')

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
                                                <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
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
                                <div class="mb-3">
                                    <button type="submit" id="submit-btn" class="btn btn-primary btn-submit">Submit</button>
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
                            <x-datepicker 
                                id="modal-event-date" 
                                name="modal-event-date" 
                                required 
                                minDate="today"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal-event-time" class="form-label">Event Time <span class="text-danger">*</span></label>
                            <select id="modal-event-time" required class="form-control default-select">
                                <option value="">Select Time</option>
                                @foreach($eventTimes as $eventTime)
                                    <option value="{{ $eventTime->id }}">{{ $eventTime->name }}</option>
                                @endforeach
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
                                @foreach($orderTypes as $orderType)
                                    <option value="{{ $orderType->id }}">{{ $orderType->name }}</option>
                                @endforeach
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
        // Pass PHP data to JavaScript
        const eventTimes = @json($eventTimes->keyBy('id'));
        const orderTypes = @json($orderTypes->keyBy('id'));
        
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

        // Initialize events array with existing orders data
        @php
            $eventsData = $relatedOrders->map(function($order) {
                $dishPrice = $order->guest_count > 0 ? ($order->estimated_cost / $order->guest_count) : 0;
                return [
                    'event_date' => $order->event_date ? $order->event_date->format('Y-m-d') : '',
                    'event_time_id' => $order->event_time_id ?? '',
                    'event_menu' => $order->event_menu ?? '',
                    'guest_count' => (int)($order->guest_count ?? 0),
                    'order_type_id' => $order->order_type_id ?? null,
                    'dish_price' => (float)number_format($dishPrice, 2, '.', ''),
                    'cost' => (float)number_format($order->estimated_cost ?? 0, 2, '.', ''),
                ];
            })->values();
        @endphp
        let events = @json($eventsData);
        let editingIndex = -1;
        let eventModal;

        document.addEventListener('DOMContentLoaded', function() {
            eventModal = new bootstrap.Modal(document.getElementById('event-modal'));

            // Initialize events table if events exist
            if (events.length > 0) {
                updateEventsTable();
                updateEventsData();
            }

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

                // Get date value from Pickadate picker (submit format)
                let eventDate = document.getElementById('modal-event-date').value;
                if (typeof jQuery !== 'undefined' && jQuery.fn.pickadate) {
                    const $dateInput = jQuery('#modal-event-date');
                    const picker = $dateInput.pickadate('picker');
                    if (picker) {
                        // Get the selected date and format it as yyyy-mm-dd
                        const selectedDate = picker.get('select');
                        if (selectedDate) {
                            // Check if selectedDate is a Date object
                            if (selectedDate instanceof Date) {
                                // Format date as yyyy-mm-dd
                                const year = selectedDate.getFullYear();
                                const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                                const day = String(selectedDate.getDate()).padStart(2, '0');
                                eventDate = year + '-' + month + '-' + day;
                            } else {
                                // If not a Date object, try to parse it or get formatted value
                                try {
                                    // Try to parse as Date if it's a date-like object
                                    const dateObj = new Date(selectedDate);
                                    if (!isNaN(dateObj.getTime())) {
                                        const year = dateObj.getFullYear();
                                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                                        const day = String(dateObj.getDate()).padStart(2, '0');
                                        eventDate = year + '-' + month + '-' + day;
                                    } else {
                                        // Try to get submit format from picker
                                        const submitValue = picker.get('value', 'yyyy-mm-dd');
                                        if (submitValue) {
                                            eventDate = submitValue;
                                        } else {
                                            // Fallback to input value and try to parse
                                            const inputValue = document.getElementById('modal-event-date').value;
                                            const parsedDate = new Date(inputValue);
                                            if (!isNaN(parsedDate.getTime())) {
                                                const year = parsedDate.getFullYear();
                                                const month = String(parsedDate.getMonth() + 1).padStart(2, '0');
                                                const day = String(parsedDate.getDate()).padStart(2, '0');
                                                eventDate = year + '-' + month + '-' + day;
                                            }
                                        }
                                    }
                                } catch (e) {
                                    // If all else fails, use input value
                                    eventDate = document.getElementById('modal-event-date').value;
                                }
                            }
                        } else {
                            // No date selected, try to get from input
                            const inputValue = document.getElementById('modal-event-date').value;
                            if (inputValue) {
                                // Try to parse and format the input value
                                const parsedDate = new Date(inputValue);
                                if (!isNaN(parsedDate.getTime())) {
                                    const year = parsedDate.getFullYear();
                                    const month = String(parsedDate.getMonth() + 1).padStart(2, '0');
                                    const day = String(parsedDate.getDate()).padStart(2, '0');
                                    eventDate = year + '-' + month + '-' + day;
                                }
                            }
                        }
                    }
                }
                
                // Final validation: ensure eventDate is in yyyy-mm-dd format
                if (eventDate && !/^\d{4}-\d{2}-\d{2}$/.test(eventDate)) {
                    // Try to parse and reformat
                    const parsedDate = new Date(eventDate);
                    if (!isNaN(parsedDate.getTime())) {
                        const year = parsedDate.getFullYear();
                        const month = String(parsedDate.getMonth() + 1).padStart(2, '0');
                        const day = String(parsedDate.getDate()).padStart(2, '0');
                        eventDate = year + '-' + month + '-' + day;
                    }
                }

                const eventData = {
                    event_date: eventDate,
                    event_time_id: parseInt(document.getElementById('modal-event-time').value),
                    event_menu: document.getElementById('modal-event-menu').value,
                    guest_count: parseInt(document.getElementById('modal-guest-count').value),
                    order_type_id: document.getElementById('modal-order-type').value ? parseInt(document.getElementById('modal-order-type').value) : null,
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
            document.querySelector('form[action="{{ route('orders.update', $order) }}"]').addEventListener('submit', function(e) {
                updateEventsData();
                
                if (events.length === 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('Please add at least one event before submitting.');
                    return false;
                }
            });

            // Handle delete modal for events
            const deleteEventModal = document.getElementById('deleteEventModal');
            if (deleteEventModal) {
                deleteEventModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const eventIndex = parseInt(button.getAttribute('data-event-index'));
                    eventIndexToDelete = eventIndex;
                });

                // Handle delete confirmation
                const deleteEventForm = document.getElementById('deleteEventModal-form');
                if (deleteEventForm) {
                    deleteEventForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        if (eventIndexToDelete >= 0) {
                            deleteEvent(eventIndexToDelete);
                            const modal = bootstrap.Modal.getInstance(deleteEventModal);
                            if (modal) {
                                modal.hide();
                            }
                            eventIndexToDelete = -1;
                        }
                    });
                }
            }
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
            // Clear date picker
            const dateInput = document.getElementById('modal-event-date');
            const picker = dateInput.pickadate ? dateInput.pickadate('picker') : null;
            if (picker) {
                picker.clear();
            }
        }

        function formatDateForDisplay(dateString) {
            // Format yyyy-mm-dd to readable format
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString; // Return as-is if invalid
            
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
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
                return `
                    <tr>
                        <td>${formatDateForDisplay(event.event_date)}</td>
                        <td>${eventTimes[event.event_time_id]?.name || '-'}</td>
                        <td>${event.event_menu}</td>
                        <td>${event.guest_count}</td>
                        <td>${event.order_type_id ? (orderTypes[event.order_type_id]?.name || '-') : '-'}</td>
                        <td>₹${event.dish_price.toFixed(2)}</td>
                        <td><strong>₹${event.cost.toFixed(2)}</strong></td>
                        <td>
                            <button type="button" onclick="editEvent(${index})" class="btn btn-sm btn-primary me-1">Edit</button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteEventModal"
                                data-event-index="${index}"
                                data-event-name="${event.event_menu} - ${formatDateForDisplay(event.event_date)}">
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function editEvent(index) {
            editingIndex = index;
            const event = events[index];
            
            // Set date picker value using Pickadate API
            const dateInput = document.getElementById('modal-event-date');
            const picker = dateInput.pickadate ? dateInput.pickadate('picker') : null;
            if (picker) {
                picker.set('select', event.event_date);
            } else {
                dateInput.value = event.event_date;
            }
            
            document.getElementById('modal-event-time').value = event.event_time_id;
            document.getElementById('modal-event-menu').value = event.event_menu;
            document.getElementById('modal-guest-count').value = event.guest_count;
            document.getElementById('modal-order-type').value = event.order_type_id || '';
            document.getElementById('modal-dish-price').value = event.dish_price;
            document.getElementById('modal-cost').value = event.cost.toFixed(2);
            document.getElementById('save-event-btn').textContent = 'Update Event';
            
            eventModal.show();
        }

        let eventIndexToDelete = -1;

        function deleteEvent(index) {
            events.splice(index, 1);
            updateEventsTable();
            updateEventsData();
        }

        function updateEventsData() {
            document.getElementById('events-data').value = JSON.stringify(events);
        }
    </script>

    <!-- Delete Event Modal -->
    <x-delete-modal 
        id="deleteEventModal" 
        title="Delete Event"
        message="Are you sure you want to delete this event?"
        delete-button-text="Delete Event"
    />
    @stack('datepicker-init')
@endsection
