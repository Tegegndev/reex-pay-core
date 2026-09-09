<script>
    "use strict";

    $(function () {
        const $subscriberIdInput = $('#subscriber-id');
        const $subscriberEmailText = $('#subscriber-email');
        const $sendMailModal = $('#sendMailModal');
        const $sendBtn = $('#sendMailBtn');
        const $spinner = $sendBtn.find('.spinner-border');
        const $btnText = $sendBtn.find('.mail-btn-text');


        // ✅ Spinner & Disable on form submit
        $(document).on('submit', '#sendMailModal form', function (e) {
            $sendBtn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $btnText.html('Sending...');
        });

        // ✅ Handle single subscriber mail
        $(document).on('click', '.single-mail', function () {
            const id = $(this).data('id');
            const email = $(this).data('email');

            $subscriberIdInput.val(id);
            $subscriberEmailText.text(email);
            $sendMailModal.modal('show');
        });


        // ✅ Reset modal on close
        $sendMailModal.on('hidden.coreui.modal', function () {
            $sendBtn.prop('disabled', false);
            $sendBtn.find('.spinner-border').addClass('d-none');
            $sendBtn.find('.mail-btn-text').removeClass('d-none');
            $subscriberIdInput.val('');
            $subscriberEmailText.text('{{ __("All Subscribers") }}');
        });
    });
</script>