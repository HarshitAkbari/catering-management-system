<!-- Search Modal -->
<div id="searchModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="closeSearchModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
            <!-- Search Input -->
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchInput" 
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    placeholder="Search orders, customers, inventory, vendors... (Press ESC to close)"
                    autocomplete="off"
                >
            </div>

            <!-- Search Results -->
            <div id="searchResults" class="max-h-96 overflow-y-auto">
                <div id="searchLoading" class="hidden text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Searching...</p>
                </div>
                <div id="searchEmpty" class="hidden text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Type to search...</p>
                </div>
                <div id="searchContent" class="hidden space-y-4">
                    <!-- Results will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let searchTimeout;
let searchModal, searchInput, searchResults, searchLoading, searchEmpty, searchContent;

document.addEventListener('DOMContentLoaded', function() {
    searchModal = document.getElementById('searchModal');
    searchInput = document.getElementById('searchInput');
    searchResults = document.getElementById('searchResults');
    searchLoading = document.getElementById('searchLoading');
    searchEmpty = document.getElementById('searchEmpty');
    searchContent = document.getElementById('searchContent');
});

window.openSearchModal = function() {
    if (!searchModal) return;
    searchModal.classList.remove('hidden');
    searchInput.focus();
    document.body.style.overflow = 'hidden';
}

window.closeSearchModal = function() {
    if (!searchModal) return;
    searchModal.classList.add('hidden');
    if (searchInput) searchInput.value = '';
    document.body.style.overflow = '';
    clearSearchResults();
};

function clearSearchResults() {
    if (!searchLoading || !searchEmpty || !searchContent) return;
    searchLoading.classList.add('hidden');
    searchEmpty.classList.add('hidden');
    searchContent.classList.add('hidden');
    searchContent.innerHTML = '';
}

function showLoading() {
    if (!searchLoading || !searchEmpty || !searchContent) return;
    searchLoading.classList.remove('hidden');
    searchEmpty.classList.add('hidden');
    searchContent.classList.add('hidden');
}

function showEmpty() {
    if (!searchLoading || !searchEmpty || !searchContent) return;
    searchLoading.classList.add('hidden');
    searchEmpty.classList.remove('hidden');
    searchContent.classList.add('hidden');
}

function showResults(data) {
    if (!searchLoading || !searchEmpty || !searchContent) return;
    searchLoading.classList.add('hidden');
    searchEmpty.classList.add('hidden');
    searchContent.classList.remove('hidden');
    
    let html = '';
    let hasResults = false;

    const sections = [
        { key: 'orders', title: 'Orders', icon: '📋', color: 'blue' },
        { key: 'customers', title: 'Customers', icon: '👥', color: 'green' },
        { key: 'inventory', title: 'Inventory', icon: '📦', color: 'purple' },
        { key: 'vendors', title: 'Vendors', icon: '🏢', color: 'orange' },
    ];

    sections.forEach(section => {
        const items = data[section.key] || [];
        if (items.length > 0) {
            hasResults = true;
            html += `
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                        <span class="mr-2">${section.icon}</span>
                        ${section.title}
                    </h3>
                    <div class="space-y-1">
            `;
            items.forEach(item => {
                html += `
                    <a href="${item.url}" class="block p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700" onclick="closeSearchModal()">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${escapeHtml(item.title)}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${escapeHtml(item.subtitle || '')}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-${section.color}-100 text-${section.color}-800 dark:bg-${section.color}-900/30 dark:text-${section.color}-300">
                                ${item.type}
                            </span>
                        </div>
                    </a>
                `;
            });
            html += `
                    </div>
                </div>
            `;
        }
    });

    if (!hasResults) {
        showEmpty();
        return;
    }

    searchContent.innerHTML = html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Search input handler
document.addEventListener('DOMContentLoaded', function() {
    const searchInputEl = document.getElementById('searchInput');
    if (!searchInputEl) return;
    
    searchInputEl.addEventListener('input', function() {
    const query = this.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        showEmpty();
        return;
    }

    showLoading();
    
    searchTimeout = setTimeout(() => {
        fetch(`{{ route('search') }}?q=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            showResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
            showEmpty();
        });
    }, 300);
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+K or Cmd+K to open search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openSearchModal();
    }
    
    // ESC to close search
    if (e.key === 'Escape' && searchModal && !searchModal.classList.contains('hidden')) {
        closeSearchModal();
    }
});

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const searchModalEl = document.getElementById('searchModal');
    if (searchModalEl) {
        searchModalEl.addEventListener('click', function(e) {
            if (e.target === searchModalEl) {
                closeSearchModal();
            }
        });
    }
});
</script>

