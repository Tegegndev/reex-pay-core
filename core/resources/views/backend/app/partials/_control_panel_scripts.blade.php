<script>
    document.addEventListener('DOMContentLoaded', function() {
        "use strict";

        // Elements
        const searchInput = document.getElementById('control-panel-search');
        const searchClear = document.getElementById('search-clear');
        const totalFeaturesSpan = document.getElementById('total-features');
        const sectionBlocks = document.querySelectorAll('.section-block');
        const featureCards = document.querySelectorAll('.feature-card');

        let originalTotalFeatures = parseInt(totalFeaturesSpan.textContent);
        let searchTimeout;

        // Search functionality
        function performSearch(query) {
            query = query.toLowerCase().trim();
            let visibleFeatures = 0;
            let visibleSections = 0;

            sectionBlocks.forEach(function(section) {
                const sectionTitle = section.querySelector('.section-title').textContent.toLowerCase();
                const featuresInSection = section.querySelectorAll('.feature-card');
                let hasVisibleFeatures = false;

                featuresInSection.forEach(function(card) {
                    const featureTitle = card.querySelector('.feature-title').textContent.toLowerCase();
                    const featureDescription = card.querySelector('.feature-description').textContent.toLowerCase();
                    const featureParent = card.querySelector('.feature-parent');
                    const parentText = featureParent ? featureParent.textContent.toLowerCase() : '';

                    const isMatch = featureTitle.includes(query) ||
                        featureDescription.includes(query) ||
                        sectionTitle.includes(query) ||
                        parentText.includes(query);

                    if (query === '' || isMatch) {
                        card.style.display = 'block';
                        hasVisibleFeatures = true;
                        visibleFeatures++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide section based on visible features
                if (hasVisibleFeatures || query === '') {
                    section.style.display = 'block';
                    visibleSections++;
                } else {
                    section.style.display = 'none';
                }

                // Update section count
                const sectionCount = section.querySelector('.section-count');
                const visibleCount = section.querySelectorAll('.feature-card[style*="display: block"], .feature-card:not([style*="display: none"])').length;
                sectionCount.textContent = query === '' ? featuresInSection.length : visibleCount;
            });

            // Update total features count
            totalFeaturesSpan.textContent = query === '' ? originalTotalFeatures : visibleFeatures;

            // Show no results message
            showNoResultsIfNeeded(visibleFeatures, query);
        }

        function showNoResultsIfNeeded(visibleFeatures, query) {
            let noResultsDiv = document.getElementById('no-search-results');

            if (visibleFeatures === 0 && query !== '') {
                if (!noResultsDiv) {
                    noResultsDiv = document.createElement('div');
                    noResultsDiv.id = 'no-search-results';
                    noResultsDiv.className = 'no-search-results';
                    noResultsDiv.innerHTML = `
                    <div class="no-results-content">
                        <div class="no-results-icon">
                            <x-icon name="search" class="icon"/>
                        </div>
                        <h3 class="no-results-title">@lang('No features found')</h3>
                        <p class="no-results-description">@lang('Try different keywords or browse all features')</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="document.getElementById('control-panel-search').value=''; document.getElementById('control-panel-search').dispatchEvent(new Event('input'));">
                            @lang('Show All Features')
                    </button>
				</div>
`;
                    document.querySelector('.control-panel-grid').appendChild(noResultsDiv);
                }
                noResultsDiv.style.display = 'block';
            } else {
                if (noResultsDiv) {
                    noResultsDiv.style.display = 'none';
                }
            }
        }

        function toggleClearButton() {
            if (searchInput.value.length > 0) {
                searchClear.classList.remove('d-none');
            } else {
                searchClear.classList.add('d-none');
            }
        }

        // Event listeners
        searchInput.addEventListener('input', function() {
            const query = this.value;
            toggleClearButton();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 200);
        });

        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            toggleClearButton();
            performSearch('');
        });

        // Initialize
        toggleClearButton();
    });
</script>