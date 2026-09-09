<script>
    "use strict";

    $(function () {
        // Cache selectors
        const $walletSelect = $("#walletSelect");
        const $currencyCode = $("#currencyCode");

        // Feature Toggle Change
        $(document).on("change", ".feature-switch", function () {
            updateFeatureStatus($(this));
        });

        // Show Modals
        $(document).on("click", ".notify-user", () => $("#notifyUserModal").modal("show"));
        $(document).on("click", ".add-money", () => $("#balanceModal").modal("show"));

        // Delete User Modal Handler
        $(document).on("click", "[data-coreui-target='#deleteUserModal']", function() {
            const userId = $(this).data("user-id");
            const username = $(this).data("username");
            const fullname = $(this).data("fullname");
            const isMerchant = $(this).data("is-merchant") === "true";
            
            // Populate modal with user data
            $("#modal-user-fullname").text(fullname);
            $("#modal-username").text(username);
            $("#confirm-username-display").text(username);
            $("#modal-user-type").text(isMerchant ? "{{ __('Merchant') }}" : "{{ __('User') }}");
            
            // Show/hide merchant specific data
            if (isMerchant) {
                $("#merchant-data-list").removeClass("d-none");
            } else {
                $("#merchant-data-list").addClass("d-none");
            }
            
            // Set form action
            $("#deleteUserForm").attr("action", "{{ route('admin.user.destroy', ':id') }}".replace(':id', userId));
            
            // Reset form state
            $("#confirmUsername").val("").removeClass("is-invalid");
            $("#confirmDeleteBtn").prop("disabled", true);
            
            // Fetch and display transaction statistics
            fetchUserTransactionStats(userId);
        });

        // Convert to Merchant Modal Handler
        $(document).on("click", "[data-coreui-target='#convertToMerchantModal']", function() {
            // Reset form state when modal opens
            $("#confirmConvertUsername").val("").removeClass("is-invalid is-valid");
            $("#confirmConvertBtn").prop("disabled", true);
        });

        // Username confirmation validation for conversion
        $("#confirmConvertUsername").on("input", function() {
            const enteredUsername = $(this).val();
            const expectedUsername = $(this).data("expected-username");
            const isValid = enteredUsername === expectedUsername;
            
            $(this).toggleClass("is-invalid", !isValid && enteredUsername.length > 0);
            $(this).toggleClass("is-valid", isValid);
            $("#confirmConvertBtn").prop("disabled", !isValid);
        });

        // Convert to Merchant Confirmation
        $("#confirmConvertBtn").on("click", function() {
            const form = $("#convertToMerchantForm");
            const enteredUsername = $("#confirmConvertUsername").val();
            const expectedUsername = $("#confirmConvertUsername").data("expected-username");

            if (enteredUsername !== expectedUsername) {
                $("#confirmConvertUsername").addClass("is-invalid");
                notifyEvs('error', "{{ __('Username does not match') }}");
                return;
            }

            if (confirm("{{ __('Are you sure you want to convert this user to a merchant?') }}")) {
                $(this).prop("disabled", true).text("{{ __('Converting...') }}");
                
                $.ajax({
                    url: form.attr("action"),
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        $("#convertToMerchantModal").modal("hide");
                        notifyEvs('success', "{{ __('User successfully converted to merchant') }}");
                        setTimeout(() => window.location.reload(), 1500);
                    },
                    error: function(xhr) {
                        let errorMessage = "{{ __('Failed to convert user to merchant') }}";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        notifyEvs('error', errorMessage);
                    },
                    complete: function() {
                        $("#confirmConvertBtn").prop("disabled", false).text("{{ __('Convert to Merchant') }}");
                    }
                });
            }
        });

        // Reset modal when closed
        $("#convertToMerchantModal").on("hidden.coreui.modal", function() {
            $("#confirmConvertUsername").val("").removeClass("is-invalid is-valid");
            $("#confirmConvertBtn").prop("disabled", true).text("{{ __('Convert to Merchant') }}");
        });

        // Username confirmation validation
        $("#confirmUsername").on("input", function() {
            const enteredUsername = $(this).val();
            const expectedUsername = $("#confirm-username-display").text();
            const isValid = enteredUsername === expectedUsername;
            
            $(this).toggleClass("is-invalid", !isValid && enteredUsername.length > 0);
            $(this).toggleClass("is-valid", isValid);
            $("#confirmDeleteBtn").prop("disabled", !isValid);
        });

        // Reset modal when closed
        $("#deleteUserModal").on("hidden.coreui.modal", function() {
            $("#confirmUsername").val("").removeClass("is-invalid is-valid");
            $("#confirmDeleteBtn").prop("disabled", true);
        });

        // Handle form submission with additional confirmation
        $("#deleteUserForm").on("submit", function(e) {
            const enteredUsername = $("#confirmUsername").val();
            const expectedUsername = $("#confirm-username-display").text();
            
            if (enteredUsername !== expectedUsername) {
                e.preventDefault();
                $("#confirmUsername").addClass("is-invalid");
                notifyEvs("error", "{{ __('Username does not match. Please type the exact username.') }}");
                return false;
            }
            
            // Final confirmation
            if (!confirm("{{ __('Are you absolutely sure you want to delete this user? This action cannot be undone.') }}")) {
                e.preventDefault();
                return false;
            }
        });

        // Wallet Selection - Update Currency Code
        $walletSelect.on("change", function () {
            const currency = $(this).find(":selected").data("currency") || "{{ __('Not Selected') }}";
            $currencyCode.text(currency);
        });

        /**
         * Fetch user transaction statistics via AJAX
         * @param {number} userId - The user ID
         */
        function fetchUserTransactionStats(userId) {
            // Reset stats display
            $("#modal-total-transactions, #modal-total-amount, #modal-successful-transactions, #modal-successful-amount").text("{{ __('Loading...') }}");
            
            $.get("{{ route('admin.user.transaction-stats', ':id') }}".replace(':id', userId))
                .done(function(response) {
                    if (response.success) {
                        $("#modal-total-transactions").text(response.data.total_transactions || 0);
                        $("#modal-total-amount").text(response.data.total_amount || "{{ siteCurrency('symbol') }}0.00");
                        $("#modal-successful-transactions").text(response.data.successful_transactions || 0);
                        $("#modal-successful-amount").text(response.data.successful_amount || "{{ siteCurrency('symbol') }}0.00");
                    } else {
                        $("#modal-total-transactions, #modal-total-amount, #modal-successful-transactions, #modal-successful-amount").text("{{ __('Error') }}");
                    }
                })
                .fail(function() {
                    $("#modal-total-transactions, #modal-total-amount, #modal-successful-transactions, #modal-successful-amount").text("{{ __('Error') }}");
                    console.error("Failed to fetch transaction statistics");
                });
        }

        /**
         * Update User Feature Status via AJAX
         * @param {object} $element - The checkbox element
         */
        function updateFeatureStatus($element) {

            if (window.APP_DEMO) {
                notifyEvs("error", "This action is disabled in demo mode.");
                // Optionally, reset the checkbox state:
                $element.prop("checked", !$element.prop("checked"));
                return;
            }

            const userId = $element.data("user-id");
            const feature = $element.data("feature");
            const status = $element.prop("checked") ? 1 : 0;

            $.post("{{ route('admin.user.feature-status.update') }}", {
                _token: "{{ csrf_token() }}",
                user_id: userId,
                feature: feature,
                status: status
            })
                .done(response => notifyEvs(response.type, response.message))
                .fail(xhr => {
                    console.error("Error:", xhr.responseText);
                    notifyEvs("error", "An error occurred while updating the feature status.");
                });
        }
    });
</script>