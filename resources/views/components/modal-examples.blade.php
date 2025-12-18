{{-- 
    Modal Component Usage Examples
    
    This file demonstrates all the different ways to use the modal component.
    You can include this in any view or use these examples as reference.
--}}

@extends('layouts.app')

@section('title', 'Modal Examples')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modal Component Examples</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Trigger Buttons</h2>
        
        <div class="flex flex-wrap gap-3">
            <!-- Standard Modal Button -->
            <button 
                type="button" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                data-modal-toggle="standard-modal"
            >
                Standard Modal
            </button>

            <!-- Large Modal Button -->
            <button 
                type="button" 
                class="px-4 py-2 bg-cyan-600 text-white rounded-md hover:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-600"
                data-modal-toggle="large-modal"
            >
                Large Modal
            </button>

            <!-- Small Modal Button -->
            <button 
                type="button" 
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600"
                data-modal-toggle="small-modal"
            >
                Small Modal
            </button>

            <!-- Full Width Modal Button -->
            <button 
                type="button" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                data-modal-toggle="full-width-modal"
            >
                Full Width Modal
            </button>

            <!-- Scrollable Modal Button -->
            <button 
                type="button" 
                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600"
                data-modal-toggle="scrollable-modal"
            >
                Scrollable Modal
            </button>
        </div>
    </div>

    <!-- Standard Modal -->
    <x-modal id="standard-modal" title="Standard Modal">
        <p class="text-gray-700 dark:text-gray-300">
            This is a standard sized modal. It has a maximum width that works well for most content.
        </p>
    </x-modal>

    <!-- Large Modal -->
    <x-modal id="large-modal" title="Large Modal" size="large">
        <div class="space-y-4">
            <p class="text-gray-700 dark:text-gray-300">
                This is a large modal with more width for displaying extensive content.
            </p>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Column 1</p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Column 2</p>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Small Modal -->
    <x-modal id="small-modal" title="Small Modal" size="small">
        <p class="text-gray-700 dark:text-gray-300">
            This is a small modal, perfect for quick confirmations or simple messages.
        </p>
    </x-modal>

    <!-- Full Width Modal -->
    <x-modal id="full-width-modal" title="Full Width Modal" size="full-width">
        <div class="space-y-4">
            <p class="text-gray-700 dark:text-gray-300">
                This modal spans almost the full width of the screen, useful for forms or detailed content.
            </p>
            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Column 1</p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Column 2</p>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Column 3</p>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- Scrollable Modal -->
    <x-modal id="scrollable-modal" title="Scrollable Modal" scrollable="true">
        <div class="space-y-4">
            <p class="text-gray-700 dark:text-gray-300">
                This modal has scrollable content. When the content exceeds the viewport height, it will scroll.
            </p>
            @for($i = 1; $i <= 20; $i++)
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Content section {{ $i }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        This is some additional content to demonstrate scrolling functionality.
                    </p>
                </div>
            @endfor
        </div>
    </x-modal>
</div>

{{-- Usage Documentation --}}
<div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Usage Documentation</h2>
    
    <div class="space-y-6">
        <div>
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Basic Usage</h3>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded text-sm overflow-x-auto"><code>&lt;x-modal id="my-modal" title="Modal Title"&gt;
    Your content here
&lt;/x-modal&gt;

&lt;button data-modal-toggle="my-modal"&gt;Open Modal&lt;/button&gt;</code></pre>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Available Props</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300">
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">id</code> (required) - Unique identifier for the modal</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">title</code> - Modal title text</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">size</code> - Modal size: 'standard', 'large', 'small', 'full-width' (default: 'standard')</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">scrollable</code> - Enable scrollable content (default: false)</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">showFooter</code> - Show/hide footer (default: true)</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">closeButtonText</code> - Close button text (default: 'Close')</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">saveButtonText</code> - Save button text (default: 'Save changes')</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">showSaveButton</code> - Show/hide save button (default: true)</li>
            </ul>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Custom Footer</h3>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded text-sm overflow-x-auto"><code>&lt;x-modal id="custom-modal" title="Custom Footer"&gt;
    Modal content
    &lt;x-slot name="footer"&gt;
        &lt;button onclick="closeModal('custom-modal')"&gt;Cancel&lt;/button&gt;
        &lt;button onclick="submitForm()"&gt;Submit&lt;/button&gt;
    &lt;/x-slot&gt;
&lt;/x-modal&gt;</code></pre>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">JavaScript Functions</h3>
            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 dark:text-gray-300">
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">openModal('modal-id')</code> - Programmatically open a modal</li>
                <li><code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">closeModal('modal-id')</code> - Programmatically close a modal</li>
            </ul>
        </div>

        <div>
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Keyboard Support</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Press <kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs">ESC</kbd> to close any open modal.
            </p>
        </div>
    </div>
</div>
@endsection

