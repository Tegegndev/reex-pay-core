<script>
    $(document).ready(function () {
        'use strict';

        let i = @json($fieldCount); // Initialize field count
	    

        actionEvent('new_payment_method_modal');
        actionEvent('edit-payment-method-modal',true);
        
	    
        function actionEvent(idName,isEdit = false) {

            let $modalForm = $('#'+idName);
            
            // Handle payment gateway selection change
            $modalForm.on('change', '#select-payment-gateway', function () {
                const gatewayId = $(this).val();
                const url = `{{ route('admin.payment.gateway-currency', ':gateway_id') }}`.replace(':gateway_id', gatewayId);

                $.get(url, function (data) {
                    $modalForm.find('#currency-list').html(data.view);
                });
            });

            // Handle currency list change
            $modalForm.on('change', '#currency-list', function () {
                const selectedCurrency = $(this).val();
                const currencies = @json(getJsonData('currencies'));

                $modalForm.find('#currency-selected').text(selectedCurrency);

                const currencySymbol = currencies['fiat'].find(currency => currency['code'] === selectedCurrency) ||
                    currencies['crypto'].find(currency => currency['code'] === selectedCurrency);

                if (currencySymbol) {
                    $modalForm.find('#currency-symbol').val(currencySymbol['symbol']);
                }
            });

            // Handle custom currency input
            $modalForm.on('keyup', '#custom_currency', function () {
                $modalForm.find('#currency-selected').text($(this).val());
            });

            // Toggle conversion rate input
            $modalForm.on('click', '#conversion_rate_live', function () {
                $modalForm.find('#conversion_rate').prop('disabled', $(this).is(':checked')).val($(this).is(':checked') ? null : $modalForm.find('#conversion_rate').val());
            });

            // Use event delegation for dynamically added fields
            $modalForm.on('click', '.delete_field', function () {
                $(this).closest('.field-remove-row').remove();
            });

            // Add new dynamic form field
            $modalForm.on('click', '#add-new-field', function () {
                const formTemplate = `@include('backend.deposit.method.partials._method_append_form_field', ['key' => '${i}'])`;
                const selector = isEdit
                    ? `.append-new-field-edit`
                    : '.append-new-field';
                $(selector).append(formTemplate);
                i++; // Increment index for unique field identification
            });
        }

        editFormByModal('edit-payment-method-modal', 'edit-payment-method-data', true, true);
    });
</script>