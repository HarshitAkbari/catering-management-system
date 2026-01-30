@props([
    'module' => null,
    'route' => null,
    'params' => [],
    'label' => 'Export',
    'class' => 'btn btn-success btn-sm',
    'icon' => 'fa fa-download',
])

@php
    $permission = $module ? "{$module}.export" : null;
    $hasPermission = $permission ? (auth()->check() && auth()->user()->hasPermission($permission)) : true;
    
    // Build route with params if provided
    $routeUrl = $route;
    if ($route && !empty($params)) {
        $routeUrl = route($route, $params);
    } elseif ($route) {
        $routeUrl = route($route);
    }
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
            <i class="{{ $icon }} me-2"></i>
        @endif
        {{ $label }}
    </a>
@endif

