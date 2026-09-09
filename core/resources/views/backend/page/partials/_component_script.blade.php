<script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            const $componentList = $('#componentList');
            const $pageComponent = $('#pageComponent');
            const $dropText = $('.drop-text');
            const $componentSearch = $('#componentSearch');

            function updateDropTextVisibility() {
                $dropText.toggleClass('d-none', $pageComponent.find('.item').length > 0);
                updateComponentListEmptyState();
            }

            function showEmptyMessage(type) {
                let $msg = $('.component-empty-text');
                if (!$msg.length) {
                    $msg = $('<div>').addClass('component-empty-text').appendTo($componentList);
                }
                $msg.text(
                    type === 'notfound' ? 'No components matched your search.' :
                        type === 'empty' ? 'No available components to add.' : ''
                ).show();
            }

            function hideEmptyMessage() {
                $('.component-empty-text').hide();
            }

            function updateComponentListEmptyState() {
                const $allItems = $componentList.find('.item');
                const $visibleItems = $allItems.filter(function () {
                    return $(this).css('display') !== 'none';
                });
                const searchText = $componentSearch.val().trim();

                if ($allItems.length === 0) {
                    showEmptyMessage(searchText ? 'notfound' : 'empty');
                } else if ($visibleItems.length === 0) {
                    showEmptyMessage(searchText ? 'notfound' : 'empty');
                } else {
                    hideEmptyMessage();
                }
            }

            // Component search
            $componentSearch.on('input', function () {
                const searchText = $(this).val().toLowerCase();
                $componentList.find('.item').each(function () {
                    const name = $(this).data('name') || '';
                    $(this).toggle(name.includes(searchText));
                });
                updateComponentListEmptyState();
            });

            // Drag back to available component list
            new Sortable($componentList[0], {
                group: 'shared',
                animation: 200,
                sort: false,
                onAdd: function (evt) {
                    const $item = $(evt.item);
                    const $toggleIcon = $item.find('.toggle-icon');
                    $toggleIcon.html(`<i class="fa-solid fa-circle-plus text-success fa-fw"></i>`);
                    $item.find('.manage-drag').attr('title', 'Add to Page');
                    updateDropTextVisibility();
                    updateComponentListEmptyState();
                    tooltipTriger()
                }
            });

            // Drop into page component area
            new Sortable($pageComponent[0], {
                group: 'shared',
                animation: 200,
                onAdd: function (evt) {
                    const $item = $(evt.item);
                    const $toggleIcon = $item.find('.toggle-icon');
                    $toggleIcon.html(`<i class="fa-solid fa-circle-plus text-danger fa-fw"></i>`);
                    $item.find('.manage-drag').attr('title', 'Remove from Page');
                    updateDropTextVisibility();
                    updateComponentListEmptyState();
                    tooltipTriger()
                }
            });

            // Click-to-move between component list and page section
            $('#componentList, #pageComponent').on('click', '.manage-drag', function () {
                const $item = $(this).closest('.item');
                const $clone = $item.clone(false); // ❗ Don't deep clone events
                const isFromList = $(this).closest('#componentList').length > 0;
                const $toggleIcon = $clone.find('.toggle-icon');

                if (isFromList) {
                    // Move to page section
                    $toggleIcon.html(`
            <i class="fa-solid fa-circle-minus text-danger fa-fw modal-tooltip"
               data-coreui-toggle="tooltip"
               title="Remove from Page"></i>
        `);
                } else {
                    // Move back to component list
                    $toggleIcon.html(`
            <i class="fa-solid fa-circle-plus text-success fa-fw modal-tooltip"
               data-coreui-toggle="tooltip"
               title="Add to Page"></i>
        `);
                }

                $item.remove();
                (isFromList ? $pageComponent : $componentList).append($clone);

                updateDropTextVisibility();
                updateComponentListEmptyState();

                // Reinitialize tooltip after move
                tooltipTriger();
            });


            // Auto-slug from title
            $('#page_title').on('input', function () {
                const slug = $(this).val().trim().toLowerCase()
                    .replace(/[^؀-ۿ\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                $('.page_slug').val(slug);
            });

            // Init on load
            updateDropTextVisibility();
            updateComponentListEmptyState();
        });
    })(jQuery);
</script>
