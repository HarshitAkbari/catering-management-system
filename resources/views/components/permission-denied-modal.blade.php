@props([
    'id' => 'permissionDeniedModal',
])

<!-- Permission Denied Modal -->
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">
                    <i class="bi bi-shield-exclamation-fill text-warning me-2"></i>
                    Permission Denied
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold">
                        You do not have permission to perform this action.
                    </h6>
                </div>
                
                <div class="alert alert-warning">
                    <p class="mb-0">
                        Please contact your administrator if you believe you should have access to this feature.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@once
<script>
// Function to show permission denied modal
function showPermissionDeniedModal(modalId = 'permissionDeniedModal') {
    const modal = document.getElementById(modalId);
    if (modal) {
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
}

// Centralized permission checking function
function checkPermissionAndProceed(permission, callback) {
    // Check if permission data is available in the page
    // We'll need to pass permission status from server-side
    const element = event?.target || event?.currentTarget;
    if (element) {
        const hasPermission = element.getAttribute('data-has-permission') === 'true';
        if (!hasPermission) {
            event?.preventDefault();
            showPermissionDeniedModal();
            return false;
        }
    }
    
    // If callback is provided and permission is granted, execute it
    if (callback && typeof callback === 'function') {
        callback();
    }
    return true;
}

// Attach click handlers to elements with data-permission attributes and permission wrappers
document.addEventListener('DOMContentLoaded', function() {
    // Handle clicks on interactive elements (links, buttons) inside permission wrappers
    document.addEventListener('click', function(event) {
        // First check if the clicked element itself has data-permission
        let element = event.target.closest('[data-permission]');
        if (element) {
            const hasPermission = element.getAttribute('data-has-permission') === 'true';
            if (!hasPermission) {
                event.preventDefault();
                event.stopPropagation();
                showPermissionDeniedModal();
                return false;
            }
        }
        
        // Check if the clicked element is inside a permission wrapper
        const wrapper = event.target.closest('[data-permission-wrapper]');
        if (wrapper) {
            const hasPermission = wrapper.getAttribute('data-has-permission') === 'true';
            // Only intercept if it's an interactive element (link, button, or clickable element)
            const interactiveElement = event.target.closest('a, button, [onclick], [role="button"]');
            if (interactiveElement && !hasPermission) {
                event.preventDefault();
                event.stopPropagation();
                showPermissionDeniedModal();
                return false;
            }
        }
    }, true); // Use capture phase to catch events early
});
</script>
@endonce

