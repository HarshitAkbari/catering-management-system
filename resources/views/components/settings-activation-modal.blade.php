@props([
    'modalId' => 'settings-activation-modal',
    'settingType' => 'setting',
    'formMethod' => 'GET',
    'csrfMethod' => 'GET',
])

{{-- Settings Activation Confirmation Modal --}}
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="{{ $modalId }}-form" method="{{ $formMethod }}" action="">
                @csrf
                @if($csrfMethod !== 'POST')
                    @method($csrfMethod)
                @endif
                
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        Activate <span id="{{ $modalId }}-setting-name"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6 class="fw-bold">
                            Are you sure you want to activate this <span id="{{ $modalId }}-setting-type">{{ $settingType }}</span>?
                        </h6>
                    </div>
                    
                    <div class="alert alert-success">
                        <div class="mb-3">
                            <p class="mb-2">
                                <strong>What happens when you activate:</strong>
                            </p>
                            <ul class="mb-0" style="list-style: none; padding-left: 0;">
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="bi bi-check-circle text-success me-2" style="font-size: 1rem; margin-top: 2px;"></i>
                                    <span>This <span class="setting-type-text">{{ $settingType }}</span> will become available for future use</span>
                                </li>
                                <li class="mb-2 d-flex align-items-start">
                                    <i class="bi bi-arrow-clockwise text-primary me-2" style="font-size: 1rem; margin-top: 2px;"></i>
                                    <span>It will appear in dropdown menus and selection lists throughout the platform</span>
                                </li>
                                <li class="mb-0 d-flex align-items-start">
                                    <i class="bi bi-database text-info me-2" style="font-size: 1rem; margin-top: 2px;"></i>
                                    <span>All previously associated data remains intact and accessible</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Activate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@once
<script>
// Function to update modal content dynamically
function showSettingsActivationModal(modalId, settingName, settingType, formAction, csrfMethod = 'GET') {
    const modal = document.getElementById(modalId);
    const form = document.getElementById(modalId + '-form');
    const settingNameSpans = modal.querySelectorAll('#' + modalId + '-setting-name, .setting-name-text');
    const settingTypeSpans = modal.querySelectorAll('#' + modalId + '-setting-type, .setting-type-text');
    
    // Update form action and method
    if (form) {
        form.setAttribute('action', formAction);
        
        // Update CSRF method if needed
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput && csrfMethod !== 'POST') {
            methodInput.value = csrfMethod;
        } else if (!methodInput && csrfMethod !== 'POST') {
            // Create method input if it doesn't exist
            const methodInputNew = document.createElement('input');
            methodInputNew.type = 'hidden';
            methodInputNew.name = '_method';
            methodInputNew.value = csrfMethod;
            form.appendChild(methodInputNew);
        }
    }
    
    // Update setting name
    settingNameSpans.forEach(span => {
        span.textContent = settingName;
    });
    
    // Update setting type
    settingTypeSpans.forEach(span => {
        span.textContent = settingType;
    });
    
    // Show the modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}
</script>
@endonce

