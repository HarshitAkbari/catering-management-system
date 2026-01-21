@props([
    'itemId' => null,
    'itemName' => null,
    'deleteUrl' => null,
    'modalId' => 'deleteModal',
    'buttonClass' => 'btn btn-danger btn-xs',
])

<button 
    type="button" 
    class="{{ $buttonClass }}"
    onclick="showDeleteModal({{ json_encode($modalId) }}, {{ json_encode($itemName) }}, {{ json_encode($deleteUrl) }})">
    Delete
</button>

