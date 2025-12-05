@props([
    'flush' => false,
    'alwaysOpen' => false,
])

@php
    $accordionId = 'accordion-' . uniqid();
    $flushClass = $flush ? 'divide-y divide-gray-200 dark:divide-gray-700' : 'space-y-2';
@endphp

<div 
    id="{{ $accordionId }}"
    class="{{ $flushClass }}"
    data-accordion="{{ $alwaysOpen ? 'open' : 'collapse' }}"
    {{ $attributes->merge(['class' => '']) }}
>
    {{ $slot }}
</div>

