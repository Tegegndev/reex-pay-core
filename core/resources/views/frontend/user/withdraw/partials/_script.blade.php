<script>
    $(document).ready(function () {
        'use strict';
        const accountInfo = {};
        const siteCurrency = "{{ siteCurrency() }}";

        // Utility: Debounce function to limit API calls
        const debounce = (func, delay) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(null, args), delay);
            };
        };

        // Fetch and populate withdrawal accounts
        const fetchWithdrawAccounts = debounce(walletId => {
            if (!walletId) return;
            const url = `${"{{ route('user.withdraw.account.index') }}"}?wallet_id=${encodeURIComponent(walletId)}`;
            $.ajax({
                url,
                type: "GET",
                success: response => updateWithdrawAccountOptions(response),
                error: () => updateWithdrawAccountOptions([], true)
            });
        }, 300);

        // Fetch withdrawal accounts on page load
        const walletSelect = $('.wallet-select');
        if (walletSelect.val() !== null) {
            fetchWithdrawAccounts(walletSelect.val());
        }

        // Update withdrawal account dropdown
        const updateWithdrawAccountOptions = (accounts, isError = false) => {
            const $accountSelect = $('.withdraw-account-select');
            if (isError || !Array.isArray(accounts) || !accounts.length) {
                $accountSelect.html('<option disabled selected>{{ __("No withdrawal accounts available for this wallet") }}</option>');
                return;
            }
            const options = accounts.map(account =>
                `<option value="${account.id}">${capitalize(account.name)}</option>`
            ).join('');
            $accountSelect.html('<option disabled selected>{{ __("Select Account") }}</option>' + options);
        };

        // Fetch account details
        const fetchAccountInfo = accountId => {
            if (!accountId) return;
            const url = "{{ route('user.withdraw.account.info', ':id') }}".replace(':id', encodeURIComponent(accountId));
            $.ajax({
                url,
                type: "GET",
                success: response => updateAccountInfo(response),
                error: () => showError('.account-info', '{{ __("Error loading account info") }}')
            });
        };

        // Update account details UI
        const updateAccountInfo = data => {
            if (!data) return;
            const curr = data.currency || siteCurrency;
            const chargeType = data.charge_type === 'fixed' ? curr : '%';
            $('.account-info').text(`Charge: ${parseFloat(data.charge || 0).toFixed(2)} ${chargeType}`);
            Object.assign(accountInfo, data);

            // Display initial Min / Max limits
            if (data.min_limit && data.max_limit) {
                showSuccess('.withdraw-amount-info', `Min: ${data.min_limit} ${curr} | Max: ${data.max_limit} ${curr}`);
            }

            // Trigger summary update if amount is already entered
            if ($('.amount-input').val()) {
                updateSummary();
            }
        };

        // Update withdrawal summary
        const updateSummary = () => {
            const amount = parseFloat($('.amount-input').val()) || 0;
            const {
                processing_time,
                charge,
                charge_type,
                conversion_rate,
                currency,
                min_limit,
                max_limit
            } = accountInfo;

            const curr = currency || siteCurrency;

            if (!conversion_rate || !currency || charge === undefined) return;

            // Validate amount
            if (amount < min_limit || amount > max_limit) {
                showError('.withdraw-amount-info', `Amount must be between ${min_limit} ${curr} and ${max_limit} ${curr}`);
                return;
            } else {
                showSuccess('.withdraw-amount-info', `Min: ${min_limit} ${curr} | Max: ${max_limit} ${curr}`);
            }

            // Calculate and display values
            const fee = charge_type === 'fixed' ? parseFloat(charge) : (amount * parseFloat(charge)) / 100;
            const total = amount + fee;
            const receivedAmount = amount * parseFloat(conversion_rate);
            const myWalletDecreased = total * parseFloat(conversion_rate);

            updateSummaryUI({
                processing_time,
                amount,
                fee,
                total,
                conversion_rate,
                receivedAmount,
                myWalletDecreased,
                currency: curr
            });
        };

        // Update summary UI
        const updateSummaryUI = ({
            processing_time,
            amount,
            fee,
            total,
            conversion_rate,
            receivedAmount,
            myWalletDecreased,
            currency
        }) => {
            $('.processing-time').text(`${processing_time || '-'}`);
            $('.summary-amount').text(`${amount.toFixed(2)} ${currency}`);
            $('.summary-charge').text(`${fee.toFixed(2)} ${currency}`);
            $('.summary-total').text(`${total.toFixed(2)} ${currency}`);
            $('.conversion-rate').text(`1 ${siteCurrency} = ${conversion_rate} ${currency}`);
            $('.received-withdraw-amount').text(`${receivedAmount.toFixed(2)} ${currency}`);
            $('.my-wallet-decreased').text(`${myWalletDecreased.toFixed(2)} ${currency}`);
        };

        // Show error message
        const showError = (selector, message) => {
            $(selector).text(message).addClass('text-danger').removeClass('text-success');
        };

        // Show success message
        const showSuccess = (selector, message) => {
            $(selector).text(message).addClass('text-success').removeClass('text-danger');
        };

        // Utility: Capitalize string
        const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);

        // Event bindings
        $(document).on('change', '.wallet-select', function () {
            const walletId = this.value;
            fetchWithdrawAccounts(walletId);
        });

        $(document).on('change', '.withdraw-account-select', function () {
            const accountId = this.value;
            fetchAccountInfo(accountId);
        });

        $(document).on('input', '.amount-input', updateSummary);
    });
</script>