<script>
    "use strict";
    function toggleCardholderTypeFields() {
        const type = document.getElementById('card_type').value;
        const personalType = @json(\App\Enums\VirtualCard\CardholderType::PERSONAL->value);
        const businessType = @json(\App\Enums\VirtualCard\CardholderType::BUSINESS->value);
        document.querySelector('.personal-fields').style.display = (type === personalType) ? '' : 'none';
        document.querySelector('.business-fields').style.display = (type === businessType) ? '' : 'none';
        // KYC beside cardholder type
        document.querySelector('.kyc-fields-wrap').style.display = (type === personalType) ? '' : 'none';
        document.getElementById('kyc-fields-dynamic').style.display = (type === personalType) ? '' : 'none';
    }
    document.getElementById('card_type').addEventListener('change', toggleCardholderTypeFields);
    window.addEventListener('DOMContentLoaded', toggleCardholderTypeFields);

    // KYC Dynamic Fields via AJAX (copied from kyc_verify)
    const $kycSelect = $('#kyc_template_id');
    const $kycFieldsDynamic = $('#kyc-fields-dynamic');
    $kycSelect.on('change', function () {
        const templateId = $(this).val();
        if (!templateId) {
            $kycFieldsDynamic.empty();
            return;
        }
        $kycFieldsDynamic.html(`
                <div class="d-flex justify-content-center my-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        const url = `{{ route('user.settings.kyc.template.details', ':id') }}`.replace(':id', templateId);
        $.get(url)
            .done(response => {
                $kycFieldsDynamic.html(response);
            });
    });
    // Optionally trigger on page load if needed
    if ($kycSelect.val()) {
        $kycSelect.trigger('change');
    }
</script>