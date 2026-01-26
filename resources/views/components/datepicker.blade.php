@props([
    'id' => null,
    'name' => null,
    'value' => null,
    'placeholder' => 'Select date',
    'required' => false,
    'minDate' => null, // Can be 'today' or a date string like '2024-01-01'
    'maxDate' => null,
    'format' => 'yyyy-mm-dd',
    'formatSubmit' => 'yyyy-mm-dd',
    'selectMonths' => true,
    'selectYears' => true,
    'class' => '',
])

@php
    // Generate unique ID if not provided
    if (!$id) {
        $id = 'datepicker-' . uniqid();
    }
    
    // Use name if provided, otherwise use id
    $inputName = $name ?? $id;
    
    // Build classes
    $classes = 'datepicker-default form-control ' . $class;
    
    // Handle minDate
    $minDateValue = null;
    if ($minDate === 'today' || $minDate === true) {
        $minDateValue = 'today';
    } elseif ($minDate) {
        $minDateValue = $minDate;
    }
    
    // Handle maxDate
    $maxDateValue = $maxDate;
@endphp

<div class="datepicker-wrapper">
    <input 
        type="text" 
        id="{{ $id }}" 
        name="{{ $inputName }}" 
        value="{{ old($inputName, $value) }}"
        class="{{ trim($classes) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes->except(['class', 'id', 'name', 'value', 'placeholder', 'required', 'minDate', 'maxDate', 'format', 'formatSubmit', 'selectMonths', 'selectYears']) }}
        data-datepicker-id="{{ $id }}"
        data-format="{{ $format }}"
        data-format-submit="{{ $formatSubmit }}"
        @if($minDateValue) data-min-date="{{ $minDateValue }}" @endif
        @if($maxDateValue) data-max-date="{{ $maxDateValue }}" @endif
        data-select-months="{{ $selectMonths ? 'true' : 'false' }}"
        data-select-years="{{ $selectYears ? 'true' : 'false' }}"
    >
</div>

@once
@push('datepicker-init')
<script>
    (function() {
        'use strict';
        
        // Initialize all datepickers on page load
        function initializeDatepickers() {
            document.querySelectorAll('.datepicker-default[data-datepicker-id]').forEach(function(input) {
                const datepickerId = input.getAttribute('data-datepicker-id');
                
                // Skip if already initialized
                if (input.hasAttribute('data-pickadate-initialized')) {
                    return;
                }
                
                // Get configuration from data attributes
                const format = input.getAttribute('data-format') || 'yyyy-mm-dd';
                const formatSubmit = input.getAttribute('data-format-submit') || format;
                const minDateAttr = input.getAttribute('data-min-date');
                const maxDateAttr = input.getAttribute('data-max-date');
                const selectMonths = input.getAttribute('data-select-months') === 'true';
                const selectYears = input.getAttribute('data-select-years') === 'true';
                
                // Build options
                const options = {
                    format: format,
                    formatSubmit: formatSubmit,
                    selectMonths: selectMonths,
                    selectYears: selectYears,
                    hiddenName: true // Creates a hidden input with submit format
                };
                
                // Add min date
                if (minDateAttr) {
                    if (minDateAttr === 'today') {
                        options.min = new Date();
                    } else {
                        options.min = new Date(minDateAttr);
                    }
                }
                
                // Add max date
                if (maxDateAttr) {
                    options.max = new Date(maxDateAttr);
                }
                
                // Initialize Pickadate
                if (typeof jQuery !== 'undefined' && jQuery.fn.pickadate) {
                    jQuery('#' + datepickerId).pickadate(options);
                    input.setAttribute('data-pickadate-initialized', 'true');
                }
            });
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeDatepickers);
        } else {
            initializeDatepickers();
        }
        
        // Re-initialize datepickers when modals are shown (for dynamically added datepickers)
        document.addEventListener('shown.bs.modal', function(event) {
            // Small delay to ensure modal is fully rendered
            setTimeout(initializeDatepickers, 100);
        });
    })();
</script>
@endpush
@endonce
