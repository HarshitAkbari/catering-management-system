@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Order</h1>

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        There were errors with your submission:
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Name</label>
                    <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('customer_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Email</label>
                    <input type="email" name="customer_email" id="customer_email" required value="{{ old('customer_email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('customer_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number</label>
                    <input type="text" name="customer_mobile" id="customer_mobile" required value="{{ old('customer_mobile') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('customer_mobile')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="address" id="address" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <button type="button" id="add-event-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Event
                    </button>
                </div>
            </div>

            <!-- Events Table -->
            <div id="events-container" class="mt-6 hidden">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Events</h2>
                @error('events')
                    <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('events.*')
                    <p class="mb-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Event Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Event Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Event Menu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Guest Count</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dish Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="events-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Events will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Hidden input for events data -->
            <input type="hidden" name="events" id="events-data" value="">

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('orders.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" id="submit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Order</button>
            </div>
        </form>
    </div>
</div>

<!-- Event Modal -->
<x-modal id="event-modal" title="Add Event" size="large">
    <form id="event-form" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="modal-event-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Date</label>
                <input type="date" id="modal-event-date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="modal-event-time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Time</label>
                <select id="modal-event-time" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select Time</option>
                    <option value="morning">Morning</option>
                    <option value="afternoon">Afternoon</option>
                    <option value="evening">Evening</option>
                    <option value="night_snack">Snack</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="modal-event-menu" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Event Menu</label>
                <input type="text" id="modal-event-menu" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="modal-guest-count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Guest Count</label>
                <input type="number" id="modal-guest-count" min="1" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="modal-order-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Type</label>
                <select id="modal-order-type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select Order Type</option>
                    <option value="full_service">Full Service</option>
                    <option value="preparation_only">Preparation Only</option>
                </select>
            </div>

            <div>
                <label for="modal-dish-price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dish Price</label>
                <input type="number" id="modal-dish-price" step="0.01" min="0" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label for="modal-cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cost</label>
                <input type="text" id="modal-cost" readonly class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 dark:bg-gray-600 dark:border-gray-600 dark:text-white" value="0.00">
            </div>
        </div>
    </form>

    <x-slot name="footer">
        <button type="button" onclick="closeModal('event-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
            Cancel
        </button>
        <button type="button" id="save-event-btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
            Add Event
        </button>
    </x-slot>
</x-modal>

<script>
    let events = [];
    let editingIndex = -1;

    // Open modal when Add Event button is clicked
    document.getElementById('add-event-btn').addEventListener('click', function() {
        editingIndex = -1;
        resetEventForm();
        openModal('event-modal');
    });

    // Calculate cost when guest count or dish price changes
    document.getElementById('modal-guest-count').addEventListener('input', calculateCost);
    document.getElementById('modal-dish-price').addEventListener('input', calculateCost);

    function calculateCost() {
        const guestCount = parseFloat(document.getElementById('modal-guest-count').value) || 0;
        const dishPrice = parseFloat(document.getElementById('modal-dish-price').value) || 0;
        const cost = guestCount * dishPrice;
        document.getElementById('modal-cost').value = cost.toFixed(2);
    }

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
            // Update existing event
            events[editingIndex] = eventData;
        } else {
            // Add new event
            events.push(eventData);
        }

        updateEventsTable();
        updateEventsData();
        closeModal('event-modal');
        resetEventForm();
    });

    function resetEventForm() {
        document.getElementById('event-form').reset();
        document.getElementById('modal-cost').value = '0.00';
        document.getElementById('save-event-btn').textContent = 'Add Event';
    }

    function updateEventsTable() {
        const tbody = document.getElementById('events-table-body');
        const container = document.getElementById('events-container');
        
        if (events.length === 0) {
            container.classList.add('hidden');
            tbody.innerHTML = '';
            return;
        }

        container.classList.remove('hidden');
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
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">${event.event_date}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">${eventTimeLabels[event.event_time] || event.event_time}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${event.event_menu}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">${event.guest_count}</td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${orderTypeLabels[event.order_type] || event.order_type || '-'}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">${event.dish_price.toFixed(2)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${event.cost.toFixed(2)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                        <button type="button" onclick="editEvent(${index})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">Edit</button>
                        <button type="button" onclick="deleteEvent(${index})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
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
        
        openModal('event-modal');
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

    // Validate events before form submission
    document.querySelector('form[action="{{ route('orders.store') }}"]').addEventListener('submit', function(e) {
        // Ensure events data is up to date
        updateEventsData();
        
        if (events.length === 0) {
            e.preventDefault();
            alert('Please add at least one event before submitting.');
            return false;
        }
        
        // Log for debugging (remove in production)
        console.log('Submitting events:', events);
        console.log('Events JSON:', document.getElementById('events-data').value);
    });
</script>
@endsection

