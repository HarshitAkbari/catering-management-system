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

@if($hasPermission && $deleteUrl)
    <button 
        type="button" 
        class="{{ $buttonClass }}"
        onclick="showDeleteModal({{ json_encode($modalId) }}, {{ json_encode($itemName) }}, {{ json_encode($deleteUrl) }})">
        Delete
    </button>
@endif

