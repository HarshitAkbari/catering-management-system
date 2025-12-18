@props([
    'id' => null,
    'title' => '',
    'size' => 'standard',
    'scrollable' => false,
    'showFooter' => true,
    'closeButtonText' => 'Close',
    'saveButtonText' => 'Save changes',
    'showSaveButton' => true,
])

@php
    if (!$id) {
        $id = 'modal-' . uniqid();
    }
    
    $sizeClasses = [
        'standard' => 'max-w-md',
        'large' => 'max-w-4xl',
        'small' => 'max-w-sm',
        'full-width' => 'max-w-full mx-4',
    ];
    
    $modalSizeClass = $sizeClasses[$size] ?? $sizeClasses['standard'];
    $scrollableClass = $scrollable ? 'overflow-y-auto max-h-[calc(100vh-8rem)]' : '';
@endphp

<!-- Modal Backdrop -->
<div 
    id="{{ $id }}-backdrop"
    class="fixed inset-0 bg-black/50 dark:bg-black/70 z-40 hidden transition-opacity duration-300"
    data-modal-backdrop="{{ $id }}"
    onclick="closeModal('{{ $id }}')"
></div>

<!-- Modal -->
<div 
    id="{{ $id }}"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
    tabindex="-1"
    data-modal="{{ $id }}"
>
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="{{ $modalSizeClass }} w-full">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all duration-300 scale-95 opacity-0">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white" id="{{ $id }}-title">
                        {{ $title }}
                    </h4>
                    <button 
                        type="button" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                        onclick="closeModal('{{ $id }}')"
                        aria-label="Close"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 {{ $scrollableClass }}">
                    {{ $slot }}
                </div>

                <!-- Modal Footer -->
                @if($showFooter)
                    <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                        @isset($footer)
                            {{ $footer }}
                        @else
                            <button 
                                type="button" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                                onclick="closeModal('{{ $id }}')"
                            >
                                {{ $closeButtonText }}
                            </button>
                            @if($showSaveButton)
                                <button 
                                    type="button" 
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors"
                                    onclick="closeModal('{{ $id }}')"
                                >
                                    {{ $saveButtonText }}
                                </button>
                            @endif
                        @endisset
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@once
<script>
    // Initialize modal functionality (only once per page)
    (function() {
        if (window.modalInitialized) return;
        window.modalInitialized = true;

        document.addEventListener('DOMContentLoaded', function() {
            // Handle ESC key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const openModals = document.querySelectorAll('[data-modal].flex');
                    openModals.forEach(modal => {
                        const modalId = modal.getAttribute('data-modal');
                        if (window.closeModal) {
                            window.closeModal(modalId);
                        }
                    });
                }
            });

            // Handle data-modal-toggle attribute
            document.querySelectorAll('[data-modal-toggle]').forEach(button => {
                button.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal-toggle');
                    if (window.openModal) {
                        window.openModal(modalId);
                    }
                });
            });
        });

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');
            
            if (modal && backdrop) {
                backdrop.classList.remove('hidden');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                
                // Trigger animation
                setTimeout(() => {
                    backdrop.classList.add('opacity-100');
                    backdrop.classList.remove('opacity-0');
                    const modalContent = modal.querySelector('.transform');
                    if (modalContent) {
                        modalContent.classList.add('scale-100', 'opacity-100');
                        modalContent.classList.remove('scale-95', 'opacity-0');
                    }
                }, 10);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');
            
            if (modal && backdrop) {
                // Trigger animation
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                const modalContent = modal.querySelector('.transform');
                if (modalContent) {
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                }
                
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    
                    // Restore body scroll
                    document.body.style.overflow = '';
                }, 300);
            }
        }

        // Make functions globally available
        window.openModal = openModal;
        window.closeModal = closeModal;
    })();
</script>
@endonce

