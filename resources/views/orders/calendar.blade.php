@extends('layouts.app')

@section('title', 'Orders Calendar')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Orders Calendar</h1>
        <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">Back to Orders</a>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div id="calendar"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: @json($orders),
        eventClick: function(info) {
            window.location.href = info.event.url;
        }
    });
    calendar.render();
});
</script>
@endsection

