<script>
    "use strict";
    $(document).ready(function () {
        $(document).on('change', '.custom-url-toggle', function () {
            const $toggle = $(this);
            const isCustom = $toggle.is(':checked');
            const idSuffix = $toggle.data('nav-id') ?? ''; // '' for create, or '_1', '_2', etc. for edit

            // Toggle input groups
            $('#slug_input_group' + idSuffix).toggle(isCustom);
            $('#page_select_group' + idSuffix).toggle(!isCustom);

            // Update label text
            $('#linked_label' + idSuffix).text(isCustom ? 'External URL' : 'Linked Page');
        });

        // Initialize dynamic form loading via modal (your custom handler)
        editFormByModal('edit-nav-modal', 'edit-append');

        // Initialize Sortable for navigation items
        new Sortable(document.getElementById('navigation-sortable'), {
            animation: 150,
            handle: '.drag-handle', // 👈 Only drag by the icon
            ghostClass: 'bg-light',
            onEnd: function () {
                let positions = [];

                $('#navigation-sortable tr').each(function (index) {
                    const id = $(this).data('id');
                    if (id) {
                        positions.push({
                            id: id,
                            order: index + 1
                        });
                    }
                });

                $.ajax({
                    url: "{{ route('admin.navigation.position-update') }}",
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        positions: positions
                    },
                    success: function (data) {
                        notifyEvs('success', data.message);
                    },
                    error: function (xhr) {
                        console.error('Failed to update order', xhr.responseText);
                    }
                });
            }
        });

    });
</script>