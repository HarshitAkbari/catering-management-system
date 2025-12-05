@props([
    'size' => 'md',
    'color' => 'primary',
    'text' => null,
    'overlay' => false,
])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
    ];
    
    $colorClasses = [
        'primary' => 'text-blue-600',
        'secondary' => 'text-gray-600',
        'success' => 'text-green-600',
        'danger' => 'text-red-600',
        'warning' => 'text-yellow-600',
        'info' => 'text-cyan-600',
    ];
    
    $spinnerSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $spinnerColor = $colorClasses[$color] ?? $colorClasses['primary'];
@endphp

@if($overlay)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="flex flex-col items-center">
            <svg class="animate-spin {{ $spinnerSize }} {{ $spinnerColor }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            @if($text)
                <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">{{ $text }}</p>
            @endif
        </div>
    </div>
@else
    <div class="flex items-center {{ $attributes->get('class') }}">
        <svg class="animate-spin {{ $spinnerSize }} {{ $spinnerColor }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        @if($text)
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $text }}</span>
        @endif
    </div>
@endif

