<script>
    'use strict';

    $(document).on('click', '.read-notification', function (e) {
        e.preventDefault();

        const $item = $(this).closest('.notification-item');
        const notificationId = $(this).data('id');
        const url = "{{ route('admin.notifications.markAsRead', ':id') }}".replace(':id', notificationId);

        $.get(url)
            .done(function () {
                $item.removeClass('unread'); // ✅ Remove unread class
                $item.find('.fa-bell.text-warning').remove(); // ✅ Optionally remove icon
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error marking as read:', textStatus, errorThrown);
            });
    });
</script>