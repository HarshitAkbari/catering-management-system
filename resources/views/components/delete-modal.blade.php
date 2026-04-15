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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h6 class="fw-bold">
                        Are you sure you want to delete <strong id="{{ $id }}-item-name">{{ $itemName ?? '' }}</strong>?
                    </h6>
                </div>
                
                <div class="alert alert-warning">
                    <div class="mb-3">
                        <p class="mb-2">
                            <strong>What happens when you delete:</strong>
                        </p>
                        <ul class="mb-0" style="list-style: none; padding-left: 0;">
                            <li class="mb-2 d-flex align-items-start">
                                <i class="bi bi-x-circle text-danger me-2" style="font-size: 1rem; margin-top: 2px;"></i>
                                <span>This item will be permanently removed from the system</span>
                            </li>
                            <li class="mb-2 d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle text-warning me-2" style="font-size: 1rem; margin-top: 2px;"></i>
                                <span>All associated data and relationships will be deleted</span>
                            </li>
                            <li class="mb-0 d-flex align-items-start">
                                <i class="bi bi-info-circle text-danger me-2" style="font-size: 1rem; margin-top: 2px;"></i>
                                <span><strong>This action cannot be undone</strong></span>
                            </li>
                        </ul>
                    </div>
                </div>
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
// Function to update modal content dynamically
function showDeleteModal(modalId, itemName, deleteUrl) {
    const modal = document.getElementById(modalId);
    const form = document.getElementById(modalId + '-form');
    const itemNameElement = modal.querySelector('#' + modalId + '-item-name');
    
    // Update form action
    if (form) {
        form.setAttribute('action', deleteUrl);
    }
    
    // Update item name
    if (itemNameElement && itemName) {
        itemNameElement.textContent = itemName;
    }
    
    // Show the modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

// Keep existing event listener for backward compatibility
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

