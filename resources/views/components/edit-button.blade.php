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

@if($routeUrl)
    <a href="{{ $routeUrl }}" 
       class="{{ $class }}" 
       title="{{ $label }}"
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

