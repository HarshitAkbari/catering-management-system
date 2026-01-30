@props([
    'module' => null,
    'route' => null,
    'model' => null,
    'label' => 'Edit',
    'class' => 'btn btn-secondary btn-xs btn-edit',
    'icon' => null,
])

@php
    $permission = $module ? "{$module}.edit" : null;
    $hasPermission = $permission ? (auth()->check() && auth()->user()->hasPermission($permission)) : true;
    
    // Build route with model if provided
    $routeUrl = $route;
    if ($route && $model) {
        $routeUrl = route($route, $model);
    } elseif ($route) {
        $routeUrl = route($route);
    }
@endphp

@if($hasPermission && $routeUrl)
    <a href="{{ $routeUrl }}" class="{{ $class }}" title="{{ $label }}">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $label }}
    </a>
@endif

