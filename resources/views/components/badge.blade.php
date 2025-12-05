@props([
    'variant' => 'primary',
    'size' => 'md',
    'pill' => false,
])

@php
    $variantClasses = [
        'primary' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'secondary' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'info' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
    ];
    
    $sizeClasses = [
        'sm' => 'text-xs px-2 py-0.5',
        'md' => 'text-sm px-2.5 py-0.5',
        'lg' => 'text-base px-3 py-1',
    ];
    
    $roundedClasses = $pill ? 'rounded-full' : 'rounded';
    
    $classes = 'inline-flex items-center font-medium ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . $roundedClasses;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

