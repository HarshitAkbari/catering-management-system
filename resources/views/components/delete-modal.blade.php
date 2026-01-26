@props([
    'id' => 'deleteModal',
    'title' => 'Confirm Deletion',
    'message' => null,
    'itemName' => null,
    'deleteUrl' => null,
    'deleteButtonText' => 'Delete',
    'cancelButtonText' => 'Cancel',
])

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($message)
                    <p>{{ $message }}</p>
                @else
                    <p>Are you sure you want to delete <strong id="{{ $id }}-item-name">{{ $itemName ?? '' }}</strong>?</p>
                @endif
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $cancelButtonText }}</button>
                <form id="{{ $id }}-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ $deleteButtonText }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@once
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle dynamic content updates for all delete modals
        // Use event delegation to catch all modal show events
        document.addEventListener('show.bs.modal', function(event) {
            const modal = event.target;
            const button = event.relatedTarget;
            
            // Check if this is a delete modal (has a form with DELETE method)
            const form = modal.querySelector('form[method="POST"]');
            if (!form || !form.querySelector('input[name="_method"][value="DELETE"]')) {
                return;
            }
            
            const modalId = modal.id;
            
            // Extract info from data-* attributes on the trigger button
            if (button) {
                const itemName = button.getAttribute('data-item-name');
                const deleteUrl = button.getAttribute('data-delete-url');
                
                // Update modal content if data attributes are present
                if (itemName) {
                    const itemNameElement = modal.querySelector('#' + modalId + '-item-name');
                    if (itemNameElement) {
                        itemNameElement.textContent = itemName;
                    }
                }
                
                // Update form action if delete URL is provided
                if (deleteUrl) {
                    form.action = deleteUrl;
                }
            }
        });
    });
</script>
@endonce

