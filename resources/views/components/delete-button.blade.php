@props([
    'itemId' => null,
    'itemName' => null,
    'deleteUrl' => null,
    'modalId' => 'deleteModal',
    'buttonClass' => 'btn btn-danger btn-xs',
    'module' => null,
])

@php
    $permission = $module ? "{$module}.delete" : null;
    $hasPermission = $permission ? (auth()->check() && auth()->user()->hasPermission($permission)) : true;
@endphp

@if($deleteUrl)
    <button 
        type="button" 
        class="{{ $buttonClass }}"
        @if($permission)
        data-permission="{{ $permission }}"
        data-has-permission="{{ $hasPermission ? 'true' : 'false' }}"
        @endif
        onclick="@if($permission && !$hasPermission)showPermissionDeniedModal(); return false;@else showDeleteModal({{ json_encode($modalId) }}, {{ json_encode($itemName) }}, {{ json_encode($deleteUrl) }});@endif">
        Delete
    </button>
@endif

