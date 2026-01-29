@php
    $accordionId = 'accordion-tips-' . uniqid();
    $headerId = 'accord-tips-header-' . uniqid();
    $collapseId = 'collapse-tips-' . uniqid();
@endphp

<div class="row mt-4">
    <div class="col-xl-12">
        <div class="accordion accordion-bordered accordion-danger-solid" id="{{ $accordionId }}">
            <div class="accordion-item">
                <div class="accordion-header rounded-lg" 
                     id="{{ $headerId }}" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#{{ $collapseId }}" 
                     aria-controls="{{ $collapseId }}" 
                     aria-expanded="true" 
                     role="button">
                    <span class="accordion-header-text">
                        <i class="bi bi-lightbulb text-white me-2"></i>
                        Tips
                    </span>
                    <span class="accordion-header-indicator"></span>
                </div>
                <div id="{{ $collapseId }}" 
                     class="collapse accordion__body show" 
                     aria-labelledby="{{ $headerId }}" 
                     data-bs-parent="#{{ $accordionId }}">
                    <div class="accordion-body-text">
                        <ul class="mb-0 list-unstyled">
                            {{ $slot }}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

