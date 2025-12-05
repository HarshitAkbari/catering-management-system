@props([
    'title' => '',
    'open' => false,
    'parentId' => null,
])

@php
    $itemId = 'accordion-item-' . uniqid();
    $parentId = $parentId ?? $attributes->get('data-parent-id', 'accordion-default');
@endphp

<h2 id="{{ $itemId }}-heading">
    <button
        type="button"
        class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800"
        data-accordion-target="#{{ $itemId }}-body"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
        aria-controls="{{ $itemId }}-body"
    >
        <span>{{ $title }}</span>
        <svg
            data-accordion-icon
            class="w-6 h-6 shrink-0"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
            ></path>
        </svg>
    </button>
</h2>
<div
    id="{{ $itemId }}-body"
    class="hidden"
    aria-labelledby="{{ $itemId }}-heading"
    data-parent="#{{ $parentId }}"
>
    <div class="p-5 border border-t-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
        {{ $slot }}
    </div>
</div>

