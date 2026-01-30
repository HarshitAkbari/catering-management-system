@props([
    'module' => null,
    'route' => null,
    'label' => 'Add',
    'class' => 'btn btn-sm btn-primary btn-add',
    'icon' => null,
])

@php
    $permission = $module ? "{$module}.create" : null;
    $hasPermission = $permission ? (auth()->check() && auth()->user()->hasPermission($permission)) : true;
@endphp

@if($hasPermission && $route)
    <a href="{{ route($route) }}" class="{{ $class }}">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $label }}
    </a>
@endif

