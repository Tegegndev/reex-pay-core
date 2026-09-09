<script>
    $(document).ready(function () {
        'use strict';
        // Initialize Sortable for footer sections
        new Sortable(document.getElementById('footer-section-sortable'), {
            animation: 150,
            handle: '.drag-handle', // 👈 Only drag by the icon
            ghostClass: 'bg-light',
            onEnd: function () {
                let positions = [];

                $('#footer-section-sortable tr').each(function (index) {
                    const id = $(this).data('id');
                    if (id) {
                        positions.push({
                            id: id,
                            order: index + 1
                        });
                    }
                });

                $.ajax({
                    url: "{{ route('admin.page.footer.section.position-update') }}",
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

        editFormByModal('edit-footer-section-modal', 'edit-footer-section-data');
    });
</script>