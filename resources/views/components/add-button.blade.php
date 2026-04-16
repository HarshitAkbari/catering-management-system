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
    $routeUrl = $route ? route($route) : null;
@endphp

@if($routeUrl)
    <a href="{{ $routeUrl }}" 
       class="{{ $class }}"
       @if($permission)
       data-permission="{{ $permission }}"
       data-has-permission="{{ $hasPermission ? 'true' : 'false' }}"
       @endif
       onclick="@if($permission && !$hasPermission)event.preventDefault(); showPermissionDeniedModal(); return false;@endif">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $label }}
    </a>
@endif

