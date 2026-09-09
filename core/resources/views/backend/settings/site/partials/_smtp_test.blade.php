<button type="button" class="btn btn-primary btn-sm" id="smtpTestBtn">
	<x-icon name="linked" height="20"/> {{ __('Test SMTP Connection') }}
</button>


@push('scripts')
	<script>
        "use strict";

        $(document).ready(function () {
            $('#smtpTestBtn').on('click', function () {

                const $button = $(this);
                const originalHtml = $button.html();
                const $errorAlert = $('#errorAlert-{{ $section }}');

                $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Testing...") }}');
                $errorAlert.addClass('d-none').text('');

                $.ajax({
                    url: '{{ route("admin.app.smtp-connection-check") }}',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        test_email: '{{ config("mail.from.address") }}'
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        $button.prop('disabled', false).html(originalHtml);

                        if (data.status === 'success') {
                            notifyEvs('success', data.message);
                            $errorAlert.addClass('d-none').text('');
                        } else {
                            $errorAlert.removeClass('d-none').text(data.message || '{{ __("Something went wrong.") }}');
                        }
                    },
                    error: function (xhr) {
                        $button.prop('disabled', false).html(originalHtml);

                        let errorMessage = '{{ __("Unexpected error occurred.") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $errorAlert.removeClass('d-none').text(errorMessage);
                    }
                });
            });
        });
	
	</script>
@endpush