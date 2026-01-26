{{-- 
    Datepicker Component Usage Examples
    
    This file demonstrates how to use the datepicker component.
--}}

{{-- Basic Usage --}}
<x-datepicker 
    id="event-date" 
    name="event_date" 
    placeholder="Select event date"
/>

{{-- With Required Field and Minimum Date (Today) --}}
<x-datepicker 
    id="event-date" 
    name="event_date" 
    required 
    minDate="today"
    placeholder="Select event date"
/>

{{-- With Value (for editing) --}}
<x-datepicker 
    id="event-date" 
    name="event_date" 
    value="{{ old('event_date', $event->date ?? '') }}"
    placeholder="Select event date"
/>

{{-- With Custom Date Range --}}
<x-datepicker 
    id="event-date" 
    name="event_date" 
    minDate="2024-01-01"
    maxDate="2024-12-31"
    placeholder="Select event date"
/>

{{-- With Custom Format --}}
<x-datepicker 
    id="event-date" 
    name="event_date" 
    format="dddd, mmmm d, yyyy"
    formatSubmit="yyyy-mm-dd"
    placeholder="Select event date"
/>

{{-- With Additional Classes and Attributes --}}
<x-datepicker 
    id="event-date" 
    name="event_date" 
    class="mb-3"
    data-custom-attr="value"
    placeholder="Select event date"
/>

