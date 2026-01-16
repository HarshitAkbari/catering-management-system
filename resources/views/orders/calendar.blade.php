@extends('layouts.app')

@section('title', 'Orders Calendar')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Orders Calendar</h4>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        initialView: 'dayGridMonth',
        weekNumbers: true,
        navLinks: true,
        nowIndicator: true,
        selectable: false,
        editable: false,
        events: @json($orders),
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        }
    });
    calendar.render();
});
</script>
@endsection

