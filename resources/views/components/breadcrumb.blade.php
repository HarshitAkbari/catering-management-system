@props(['breadcrumbs'])

@if(!empty($breadcrumbs))
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="flex items-center">
                    @if($index > 0)
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    
                    @if($breadcrumb['url'] && !$breadcrumb['active'])
                        <a 
                            href="{{ $breadcrumb['url'] }}" 
                            class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 font-medium"
                        >
                            {{ $breadcrumb['label'] }}
                        </a>
                    @else
                        <span class="text-gray-900 dark:text-white font-semibold {{ $breadcrumb['active'] ? '' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $breadcrumb['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
