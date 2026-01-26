@props([
    'itemId' => null,
    'itemName' => null,
    'deleteUrl' => null,
    'modalId' => 'deleteModal',
    'buttonClass' => 'btn btn-danger btn-sm',
    'iconOnly' => true,
    'buttonText' => null,
])

<button 
    type="button" 
    class="{{ $buttonClass }}"
    title="Delete"
    data-bs-toggle="modal" 
    data-bs-target="#{{ $modalId }}"
    @if($itemName) data-item-name="{{ $itemName }}" @endif
    @if($deleteUrl) data-delete-url="{{ $deleteUrl }}" @endif>
    @if($iconOnly)
        <i class="bi bi-trash"></i>
    @else
        @if($buttonText)
            {{ $buttonText }}
        @else
            <i class="bi bi-trash me-1"></i>Delete
        @endif
    @endif
</button>

