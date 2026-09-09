<script>
    $(document).ready(function () {
        'use strict';

        function toggleFields($selector) {
            const selected = $selector.val();
            const idSuffix = $selector.data('footer-id') ?? '';

            // Hide all groups first
            $('#external-url-group' + idSuffix).addClass('d-none');
            $('#page-dropdown-group' + idSuffix).addClass('d-none');
            $('#social-dropdown-group' + idSuffix).addClass('d-none');
            $('#footer-content-group' + idSuffix + ' .content-textarea-group').addClass('d-none');

            // Reset required fields
            $('#external_url_input' + idSuffix).prop('required', false);
            $(`#footer-content-group${idSuffix} textarea[name^="content"]`).prop('required', false);

            if (selected === 'none') {
                // Show Content fields
                $('#footer-content-group' + idSuffix + ' .content-textarea-group').removeClass('d-none');
                $(`#footer-content-group${idSuffix} textarea[name^="content"]`).first().prop('required', true);
            } else if (selected === 'external_url') {
                // Show external URL input
                $('#external-url-group' + idSuffix).removeClass('d-none');
                $('#external_url_input' + idSuffix).prop('required', true);
            } else if (selected === 'page') {
                // Show Page dropdown
                $('#page-dropdown-group' + idSuffix).removeClass('d-none');
            } else if (selected === 'social') {
                // Show Social dropdown
                $('#social-dropdown-group' + idSuffix).removeClass('d-none');
            }
        }

        // For Create form
        toggleFields($('.url-type-toggle'));

        $(document).on('change', '.url-type-toggle', function () {
            toggleFields($(this));
        });
        $(document).on('click', '.edit-modal', function () {
            toggleFields($('.url-type-toggle'));

        });


        // Initialize Sortable for footer items
        new Sortable(document.getElementById('footer-item-sortable'), {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-light',
            onEnd: function () {
                let positions = [];

                $('#footer-item-sortable tr').each(function (index) {
                    const id = $(this).data('id');
                    if (id) {
                        positions.push({
                            id: id,
                            order: index + 1
                        });
                    }
                });

                $.ajax({
                    url: "{{ route('admin.page.footer.item.position-update') }}",
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        positions: positions
                    },
                    success: function (data) {
                        notifyEvs('success', data.message);
                    },
                    error: function (xhr) {
                        console.error('Failed to update item order', xhr.responseText);
                    }
                });
            }
        });

        editFormByModal('edit-footer-item-modal', 'edit-footer-item-data');
    });
</script>