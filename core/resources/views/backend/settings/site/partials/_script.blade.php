<script>
    $(document).ready(function () {
        'use strict';
        // Cache jQuery selectors
        const $siteCurrencyType = $('#site_currency_type');
        const $siteCurrency = $('#site_currency');
        const $currencySymbol = $('#currency_symbol');

        // Initial currencies data
        let currencies = @json(getJsonData('currencies'));

        // Handle currency type change
        $siteCurrencyType.on('change', function () {
            let currencyType = $(this).val();
            let selectedCurrencies = currencies[currencyType];

            // Clear and populate currency options
            $siteCurrency.empty();
            $.each(selectedCurrencies, function (key, currency) {
                $siteCurrency.append(
                    $('<option></option>').val(currency['code']).text(`${currency['code']} : ${currency['name']}`)
                );
            });
        });

        // Handle currency change
        $siteCurrency.on('change', function () {
            let selectedCurrency = $(this).val();

            // Find the selected currency symbol
            let currencySymbol = currencies['fiat'].find(currency => currency['code'] === selectedCurrency) ||
                currencies['crypto'].find(currency => currency['code'] === selectedCurrency);

            // Update the currency symbol input
            if (currencySymbol) {
                $currencySymbol.val(currencySymbol['symbol']);
            }
        });
    });
</script>