<form class="d-none d-md-flex align-items-center ms-2 position-relative admin-menu-search-form" role="search" id="admin-menu-search-form">
    <div class="input-group shadow-sm admin-search-group">
        <span class="input-group-text bg-body-secondary border-0 px-2 admin-search-addon" id="search-addon">
            <x-icon name="search" class="icon icon-lg text-secondary" id="search-icon"/>
            <div class="spinner-border spinner-border-sm text-secondary d-none" id="search-loading" role="status">
                <span class="visually-hidden">@lang('Searching')...</span>
            </div>
        </span>
        <input id="admin-header-search" class="form-control bg-body-secondary border-0 ps-1 pe-4 py-1 small admin-search-input" type="text" placeholder="@lang('Search Features')..." aria-label="Search Admin Menus" aria-describedby="search-addon" data-coreui-i18n="[placeholder]search" autocomplete="off">
        <button id="admin-header-search-clear" type="reset" class="btn btn-link px-2 py-0 position-absolute end-0 top-50 translate-middle-y d-none admin-search-clear" tabindex="-1" aria-label="Clear search">
            <x-icon name="x-circle" class="icon icon-lg text-muted"/>
        </button>
    </div>
    
    <!-- Search Results Dropdown -->
    <div id="admin-menu-search-results" class="admin-menu-search-results position-absolute top-100 start-0 w-100 bg-white border border-light-subtle rounded-bottom shadow-lg d-none">
        <div class="p-2">
            <div id="search-results-container">
                <!-- Results will be populated here -->
            </div>
            <div id="no-results" class="text-center py-3 text-muted small d-none">
                <x-icon name="search" class="icon icon-md mb-1"/>
                <div>@lang('No results found')</div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        "use strict";
        
        
        // Elements
        const form = document.getElementById('admin-menu-search-form');
        const input = document.getElementById('admin-header-search');
        const clearBtn = document.getElementById('admin-header-search-clear');
        const resultsContainer = document.getElementById('admin-menu-search-results');
        const searchResultsContent = document.getElementById('search-results-container');
        const noResults = document.getElementById('no-results');
        const searchIcon = document.getElementById('search-icon');
        const loadingIcon = document.getElementById('search-loading');
        
        if (!input || !resultsContainer) {
            //console.error('Search elements not found');
            return;
        }
        
        let searchTimeout;
        let currentResults = [];
        let selectedIndex = -1;
        
        // API endpoint
        const searchUrl = '{{ route("admin.app.menu-search") }}';
        // console.log('Search URL:', searchUrl);
        
        // Toggle clear button
        function toggleClearBtn() {
            if (input.value.length > 0) {
                clearBtn && clearBtn.classList.remove('d-none');
            } else {
                clearBtn && clearBtn.classList.add('d-none');
                hideResults();
            }
        }
        
        // Show/hide loading
        function showLoading() {
            searchIcon && searchIcon.classList.add('d-none');
            loadingIcon && loadingIcon.classList.remove('d-none');
        }
        
        function hideLoading() {
            searchIcon && searchIcon.classList.remove('d-none');
            loadingIcon && loadingIcon.classList.add('d-none');
        }
        
        // Show/hide results
        function hideResults() {
            resultsContainer.classList.add('d-none');
            selectedIndex = -1;
            currentResults = [];
        }
        
        function showResults() {
            resultsContainer.classList.remove('d-none');
        }
        
        // Perform search
        function performSearch(query) {
            //console.log('Searching for:', query);
            
            if (query.length < 2) {
                hideResults();
                return;
            }
            
            showLoading();
            
            const url = `${searchUrl}?query=${encodeURIComponent(query)}`;
            //console.log('Fetch URL:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                //console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                //console.log('Response data:', data);
                hideLoading();
                if (data.success && data.menus && data.menus.length > 0) {
                    displayResults(data.menus);
                } else {
                    showNoResults();
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                hideLoading();
                showNoResults();
            });
        }
        
        // Display results
        function displayResults(menus) {
            //console.log('Displaying results:', menus);
            currentResults = menus;
            selectedIndex = -1;
            
            if (menus.length === 0) {
                showNoResults();
                return;
            }
            
            let html = '';
            menus.forEach(function(menu, index) {
                html += `
                    <div class="menu-search-item px-3 py-2 border-bottom border-light-subtle"
                         data-index="${index}" data-route="${menu.route}" data-url="${menu.url}"
                         style="cursor: pointer; transition: background-color 0.2s;">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-medium">${menu.label}</div>
                                <div class="small text-muted">${menu.section}${menu.parent ? ' • ' + menu.parent : ''}</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </div>
                    </div>
                `;
            });
            
            searchResultsContent.innerHTML = html;
            noResults.classList.add('d-none');
            showResults();
            
            // Add click listeners
            document.querySelectorAll('.menu-search-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    const route = this.getAttribute('data-route');
                    const url = this.getAttribute('data-url');
                    //console.log('Navigating to route:', route, 'URL:', url);
                    navigateToRoute(url);
                });
                
                item.addEventListener('mouseenter', function() {
                    selectedIndex = parseInt(this.getAttribute('data-index'));
                    updateSelection();
                });
            });
        }
        
        // Show no results
        function showNoResults() {
            searchResultsContent.innerHTML = '';
            noResults.classList.remove('d-none');
            showResults();
        }
        
        // Navigate to route
        function navigateToRoute(url) {
            //console.log('Navigating to URL:', url);
            
            try {
                if (url && url.startsWith('http')) {
                    window.location.href = url;
                } else {
                    // Fallback to dashboard
                    console.error('Invalid URL provided:', url);
                    window.location.href = '{{ route("admin.dashboard") }}';
                }
            } catch (error) {
                console.error('Navigation error:', error);
                // Fallback
                window.location.href = '{{ route("admin.dashboard") }}';
            }
        }
        
        // Update selection styling
        function updateSelection() {
            document.querySelectorAll('.menu-search-item').forEach(function(item, index) {
                if (index === selectedIndex) {
                    item.style.backgroundColor = '#e3f2fd';
                } else {
                    item.style.backgroundColor = '';
                }
            });
        }
        
        // --- Fix: Make clear button always work, even when dropdown is open ---
        function handleClear(e) {
            e.preventDefault();
            e.stopPropagation();
            input.value = '';
            toggleClearBtn();
            hideResults();
            setTimeout(() => input.focus(), 0);
        }
        if (clearBtn) {
            clearBtn.addEventListener('pointerdown', handleClear);
            clearBtn.addEventListener('mousedown', handleClear);
            clearBtn.addEventListener('click', handleClear);
        }
    
        // Prevent dropdown from closing if clearBtn is pressed
        form.addEventListener('mousedown', function(e) {
            if (e.target === clearBtn) {
                e.preventDefault();
            }
        }, true);
    
        // Event listeners
        input.addEventListener('input', function() {
            const query = this.value.trim();
            toggleClearBtn();
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => performSearch(query), 300);
        });
        
        input.addEventListener('keydown', function(e) {
            if (!resultsContainer.classList.contains('d-none') && currentResults.length > 0) {
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        selectedIndex = Math.min(selectedIndex + 1, currentResults.length - 1);
                        updateSelection();
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        selectedIndex = Math.max(selectedIndex - 1, -1);
                        updateSelection();
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (selectedIndex >= 0 && currentResults[selectedIndex]) {
                            navigateToRoute(currentResults[selectedIndex].url);
                        }
                        break;
                    case 'Escape':
                        hideResults();
                        input.blur();
                        break;
                }
            }
        });
    
        input.addEventListener('input', toggleClearBtn);
        input.addEventListener('focus', toggleClearBtn);
        input.addEventListener('change', toggleClearBtn);
    
        // Hide on outside click
        document.addEventListener('click', function(e) {
            if (!form.contains(e.target)) {
                hideResults();
            }
        });
        
        // Initialize
        toggleClearBtn();
        console.log('Admin search initialized successfully');
    });
</script>
@endpush