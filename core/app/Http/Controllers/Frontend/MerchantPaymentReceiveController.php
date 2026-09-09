<?php

namespace App\Http\Controllers\Frontend;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\EnvironmentMode;
use App\Enums\MethodType;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Exceptions\NotifyErrorException;
use App\Http\Controllers\Controller;
use App\Models\DepositMethod;
use App\Models\Merchant;
use App\Models\Transaction as TransactionModel;
use App\Models\Voucher;
use App\Services\Handlers\PaymentHandler;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Payment;
use Throwable;
use Transaction;
use Wallet;

class MerchantPaymentReceiveController extends Controller
{
    /**
     * Display the payment checkout page.
     *
     * @return Factory|Application|\Illuminate\View\View|object|View
     */
    public function paymentCheckoutSigned(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, __('Invalid or expired link'));
        }

        $token = $request->query('token');

        if (! $token) {
            abort(403, __('Invalid transaction request'));
        }

        try {
            $trxId = Crypt::decryptString($token);
        } catch (Exception $e) {
            Log::error($e);
            abort(403, __('Invalid or expired token'));
        }

        $transaction = Transaction::findTransaction($trxId);

        if (! $transaction || $transaction->status !== TrxStatus::PENDING) {
            abort(404, 'Not Valid Transaction');
        }

        $merchant = Merchant::findOrFail($transaction->trx_data['merchant_id'])
            ->only(['business_name', 'business_logo']);

        // Detect environment mode from transaction data
        $environment = $this->detectEnvironmentMode($transaction);
        $isSandbox = $environment->isSandbox();

        $data = array_merge($transaction->trx_data, $merchant, [
            'payment_amount' => "{$transaction->payable_amount} {$transaction->payable_currency}",
            'environment' => $environment->value,
            'is_sandbox' => $isSandbox,
            'environment_label' => $environment->getLabel(),
            'environment_badge_class' => $environment->getBadgeClass(),
        ]);

        $paymentMethods = DepositMethod::active()
            ->where('type', MethodType::AUTOMATIC)
            ->where('currency', 'UGX')
            ->get();

        // Add sandbox-specific transaction marking
        if ($isSandbox) {
            $data['sandbox_notice'] = __('This is a sandbox transaction. No real money will be processed.');
            $data['sandbox_transaction_id'] = $transaction->trx_id;
            
            // Log sandbox payment attempt
            Log::info('Sandbox payment checkout accessed', [
                'transaction_id' => $transaction->trx_id,
                'merchant_id' => $transaction->trx_data['merchant_id'],
                'amount' => $transaction->payable_amount,
                'currency' => $transaction->payable_currency,
                'environment' => $environment->value,
            ]);
        }

        return view('general.merchant.payment_checkout', compact('data', 'paymentMethods', 'trxId'));
    }

    public function paymentCheckoutPublic(string $merchant, string $token)
    {
        // 1. Retrieve the transaction using the custom scope
        $transaction = TransactionModel::byTrxToken($token)->firstOrFail();

        // 2. Ensure the transaction is still pending
        if ($transaction->status !== TrxStatus::PENDING) {
            abort(404, __('Invalid or already processed transaction.'));
        }

        // 3. Check if the transaction has expired using model method
        if ($transaction->isExpired()) {
            abort(403, __('This payment link has expired.'));
        }

        // 4. Retrieve the merchant from the transaction data
        $merchantModel = Merchant::find($transaction->trx_data['merchant_id']);

        // 5. Match the URL-provided merchant slug with actual merchant's business name
        if (! $merchantModel || Str::slug($merchantModel->business_name) !== $merchant) {
            abort(403, __('Merchant mismatch or not found.'));
        }

        // Detect environment mode from transaction data
        $environment = $this->detectEnvironmentMode($transaction);
        $isSandbox = $environment->isSandbox();

        // 6. Prepare data to pass to the payment view
        $data = array_merge($transaction->trx_data, [
            'business_name'  => $merchantModel->business_name,
            'business_logo'  => $merchantModel->business_logo,
            'payment_amount' => "{$transaction->payable_amount} {$transaction->payable_currency}",
            'expires_at'     => $transaction->formattedExpiresAt(), // Optional: formatted for UI display
            'environment' => $environment->value,
            'is_sandbox' => $isSandbox,
            'environment_label' => $environment->getLabel(),
            'environment_badge_class' => $environment->getBadgeClass(),
        ]);

        // Add sandbox-specific handling
        if ($isSandbox) {
            $data['sandbox_notice'] = __('This is a sandbox transaction. No real money will be processed.');
            $data['sandbox_transaction_id'] = $transaction->trx_id;
            
            // Log sandbox payment attempt
            Log::info('Sandbox payment checkout (public) accessed', [
                'transaction_id' => $transaction->trx_id,
                'merchant_id' => $transaction->trx_data['merchant_id'],
                'merchant_slug' => $merchant,
                'amount' => $transaction->payable_amount,
                'currency' => $transaction->payable_currency,
                'environment' => $environment->value,
            ]);
        }

        // 7. Load available automatic deposit methods based on transaction currency
        $paymentMethods = DepositMethod::active()
            ->where('type', MethodType::AUTOMATIC)
            ->where('currency', 'UGX')
            ->get();

        $trxId = $transaction->trx_id;

        // 8. Return the checkout view with the necessary data
        return view('general.merchant.payment_checkout', compact('data', 'paymentMethods', 'trxId'));
    }

    /**
     * Process the selected payment method.
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'trx_id'          => 'required',
            'selected_method' => 'required',
        ]);

        $transaction = Transaction::findTransaction($validated['trx_id']);

        if (! $transaction || $transaction->status !== TrxStatus::PENDING || $transaction->isExpired()) {
            abort(404);
        }

        // Detect environment mode
        $environment = $this->detectEnvironmentMode($transaction);
        $isSandbox = $environment->isSandbox();

        // Log payment processing start
        Log::info('Payment processing started', [
            'transaction_id' => $validated['trx_id'],
            'payment_method' => $validated['selected_method'],
            'environment' => $environment->value,
            'is_sandbox' => $isSandbox,
        ]);

        if ($validated['selected_method'] === MethodType::SYSTEM->value) {
            $token = Crypt::encryptString($validated['trx_id']); // Encrypt trxId for extra security

            $signedUrl = URL::temporarySignedRoute(
                'payment.wallet.pay',
                now()->addMinutes(10),
                ['token' => $token]
            );

            return redirect()->away($signedUrl);
        }

        // Sandbox auto-completion for gateway payments
        if ($isSandbox) {
            // Auto-complete payment for sandbox mode
            $merchantName = $transaction->trx_data['merchant_name'];
            $description = __('SANDBOX: Payment via :provider from :merchant (Auto-completed)', [
                'provider' => title($validated['selected_method']), 
                'merchant' => $merchantName
            ]);

            // Complete the transaction immediately
            Transaction::completeTransaction($validated['trx_id'], null, $description);

            // Log sandbox auto-completion
            Log::info('Sandbox gateway payment auto-completed', [
                'transaction_id' => $validated['trx_id'],
                'payment_method' => $validated['selected_method'],
                'environment' => $environment->value,
            ]);

            notifyEvs('success', __('Sandbox payment completed successfully!'));

            // Redirect to success page
            $successUrl = $transaction->trx_data['success_redirect'] ?? route('status.success');
            return redirect()->away($successUrl);
        }

        $provider = title($validated['selected_method']);

        // Update transaction with environment-aware description
        $merchantName = $transaction->trx_data['merchant_name'];
        $description = __('Payment via :provider from :merchant', ['provider' => $provider, 'merchant' => $merchantName]);

        $transaction->update([
            'provider'    => $provider,
            'description' => $description,
        ]);

        // Get the payment gateway URL from the service
        $redirectUrl = Payment::paymentWithPaymentMethod(
            $validated['selected_method'],
            $transaction
        );

        // Log payment gateway redirect
        Log::info('Payment gateway redirect', [
            'transaction_id' => $validated['trx_id'],
            'payment_method' => $validated['selected_method'],
            'provider' => $provider,
            'environment' => $environment->value,
            'redirect_url' => is_string($redirectUrl) ? substr($redirectUrl, 0, 100) : 'HTML_CONTENT',
        ]);

        // Redirect the user to the payment gateway
        if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
            // Redirect the user to the payment gateway for URL
            return redirect()->away($redirectUrl);
        } else {
            // Return the HTML view directly if $redirectUrl is HTML content
            return response($redirectUrl, 200)->header('Content-Type', 'text/html');
        }
    }

    /**
     * Complete payment using wallet credentials provided by the user.
     *
     * @throws NotifyErrorException
     */
    public function completePayment(Request $request): RedirectResponse
    {
        $voucherResult = $this->voucherPayment($request);
        if ($voucherResult instanceof \Illuminate\Http\RedirectResponse) {
            return $voucherResult;
        }
        // Default: Wallet payment (existing logic)
        $validated = $request->validate([
            'trx_id'    => 'required',
            'password'  => 'required',
            'wallet_id' => 'required',
        ]);

        $merchantTransaction = Transaction::findTransaction($validated['trx_id']);
        if (!$merchantTransaction) {
            throw new NotifyErrorException(__('Transaction not found.'));
        }

        $trxData = $merchantTransaction->trx_data;
        $merchant = Merchant::findOrFail($trxData['merchant_id']);

        // Detect environment mode
        $environment = $this->detectEnvironmentMode($merchantTransaction);
        $isSandbox = $environment->isSandbox();

        // Handle sandbox test credentials
        if ($isSandbox && $validated['wallet_id'] === '123456789' && $validated['password'] === 'demo123') {
            // Auto-complete payment for demo credentials
            $description = __('SANDBOX: Payment via Demo Wallet from :merchant customer (Auto-completed)', 
                ['merchant' => $merchant->business_name]);

            Transaction::completeTransaction($merchantTransaction->trx_id, null, $description);

            // Log sandbox demo payment
            Log::info('Sandbox demo wallet payment completed', [
                'transaction_id' => $merchantTransaction->trx_id,
                'wallet_id' => $validated['wallet_id'],
                'environment' => $environment->value,
            ]);

            notifyEvs('success', __('Demo wallet payment completed successfully!'));

            $redirectUrl = $trxData['success_redirect'] ?? route('status.success');
            return redirect()->away($redirectUrl);
        }

        $userWallet = Wallet::getWalletByUniqueId($validated['wallet_id']);
        $user = $userWallet->user;

        if (! Hash::check($validated['password'], $user->password)) {
            throw new NotifyErrorException(__('Incorrect password for this wallet user.'));
        }

        return $this->processMerchantTransaction($userWallet, $merchantTransaction, $user, $merchant);
    }

    /**
     * Complete payment using the currently authenticated user's wallet.
     *
     * @throws NotifyErrorException
     */
    public function payWithAccount(Request $request): RedirectResponse
    {
        if ($request->isMethod('get')) {
            $token = $request->get('token'); // Get the transaction ID from the query parameter $trxId

            $payload = Payment::verifyTokenAndGetData($token);

            if (! isset($payload['trx_id'])) {
                throw new NotifyErrorException('Invalid or expired token.');
            }
            $trxId = $payload['trx_id'];

        } else {
            $validated = $request->validate([
                'trx_id' => 'required',
            ]);
            $trxId = $validated['trx_id'];
        }

        $merchantTransaction = Transaction::findTransaction($trxId);

        if (! $merchantTransaction) {
            abort(404);
        }
        $trxData = $merchantTransaction->trx_data;

        $merchant = Merchant::findOrFail($trxData['merchant_id']);
        
        // Detect environment mode for auto-completion
        $environment = $this->detectEnvironmentMode($merchantTransaction);
        $isSandbox = $environment->isSandbox();

        // Auto-complete login payment for sandbox mode
        if ($isSandbox) {
            $description = __('SANDBOX: Payment via Login from :merchant customer (Auto-completed)', 
                ['merchant' => $merchant->business_name]);

            Transaction::completeTransaction($merchantTransaction->trx_id, null, $description);

            // Log sandbox login payment
            Log::info('Sandbox login payment auto-completed', [
                'transaction_id' => $merchantTransaction->trx_id,
                'environment' => $environment->value,
            ]);

            notifyEvs('success', __('Login payment completed successfully!'));

            $redirectUrl = $trxData['success_redirect'] ?? route('status.success');
            return redirect()->away($redirectUrl);
        }

        $user = auth()->user();
        $userWallet = Wallet::getWalletByUserId($user->id, $merchant->currency->code);

        return $this->processMerchantTransaction($userWallet, $merchantTransaction, $user, $merchant);
    }

    public function walletPayment(Request $request, $token)
    {
        // Validate the signed route (expiry, tampering)
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired payment link.');
        }

        // Decrypt trx_id from token
        try {
            $trx_id = Crypt::decryptString($token);
        } catch (\Exception $e) {
            abort(403, 'Invalid or expired token.');
        }

        // Find transaction and ensure it's pending (one-time use)
        $transaction = Transaction::findTransaction($trx_id);
        if (! $transaction || $transaction->status !== TrxStatus::PENDING) {
            abort(404, 'Invalid or already processed transaction.');
        }

        // Detect environment mode
        $environment = $this->detectEnvironmentMode($transaction);
        $isSandbox = $environment->isSandbox();

        // (Optional) Rate limit by IP or add attempt logging here
        $data = array_merge($transaction->trx_data, [
            'payment_amount' => "{$transaction->payable_amount} {$transaction->payable_currency}",
            'currency'       => $transaction->payable_currency,
            'environment' => $environment->value,
            'is_sandbox' => $isSandbox,
            'environment_label' => $environment->getLabel(),
            'environment_badge_class' => $environment->getBadgeClass(),
        ]);

        // Add sandbox-specific data
        if ($isSandbox) {
            $data['sandbox_notice'] = __('This is a sandbox transaction. No real money will be processed.');
            $data['sandbox_transaction_id'] = $transaction->trx_id;
            
            // Log sandbox wallet payment access
            Log::info('Sandbox wallet payment accessed', [
                'transaction_id' => $transaction->trx_id,
                'merchant_id' => $transaction->trx_data['merchant_id'],
                'amount' => $transaction->payable_amount,
                'currency' => $transaction->payable_currency,
                'environment' => $environment->value,
            ]);
        }

        return view('general.merchant.payment_wallet', [
            'data'  => $data,
            'trxId' => $trx_id,
        ]);
    }

    /**
     * Handle a failed transaction by sending an IPN notification and redirecting.
     */
    protected function handleFailure($transaction, string $errorMessage, ?string $description = null): RedirectResponse
    {
        // Detect environment for proper logging
        $environment = $this->detectEnvironmentMode($transaction);
        $isSandbox = $environment->isSandbox();
        
        // Log failure with environment context
        Log::error('Payment transaction failed', [
            'transaction_id' => $transaction->trx_id ?? 'unknown',
            'environment' => $environment->value,
            'is_sandbox' => $isSandbox,
            'error_message' => $errorMessage,
            'description' => $description,
        ]);

        Transaction::failTransaction($transaction->trx_id, $errorMessage, $description);

        return redirect()->away($transaction->trx_data['cancel_redirect'] ?? route('status.cancel'));
    }

    /**
     * Handle voucher payment with sandbox environment support.
     */
    private function voucherPayment(Request $request)
    {
        $isVoucher = $request->has('voucher_code') && ! empty($request->input('voucher_code'));

        if (! $isVoucher) {
            return null;
        }

        $validated = $request->validate([
            'trx_id'       => 'required',
            'voucher_code' => 'required|string|max:32',
        ]);

        $trxId       = $validated['trx_id'];
        $voucherCode = $validated['voucher_code'];

        $merchantTransaction = Transaction::findTransaction($trxId);
        if (! $merchantTransaction || $merchantTransaction->status !== TrxStatus::PENDING) {
            throw new NotifyErrorException(__('Invalid or expired transaction.'));
        }

        $trxData  = $merchantTransaction->trx_data;
        $merchant = Merchant::findOrFail($trxData['merchant_id']);

        // Detect environment mode
        $environment = $this->detectEnvironmentMode($merchantTransaction);
        $isSandbox = $environment->isSandbox();

        // Handle sandbox test voucher
        if ($isSandbox && $voucherCode === 'TESTVOUCHER') {
            // Auto-complete payment for demo voucher
            $description = __('SANDBOX: Payment via Demo Voucher from :merchant customer (Auto-completed)', 
                ['merchant' => $merchant->business_name]);

            Transaction::completeTransaction($merchantTransaction->trx_id, null, $description);

            // Log sandbox demo voucher payment
            Log::info('Sandbox demo voucher payment completed', [
                'transaction_id' => $merchantTransaction->trx_id,
                'voucher_code' => $voucherCode,
                'environment' => $environment->value,
            ]);

            notifyEvs('success', __('Demo voucher payment completed successfully!'));

            $redirectUrl = $trxData['success_redirect'] ?? route('status.success');
            return redirect()->to($redirectUrl);
        }

        DB::beginTransaction();
        try {
            $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();
            if (! $voucher || ! $voucher->isValid()) {
                throw new NotifyErrorException(__('Invalid or already redeemed voucher.'));
            }

            if ($voucher->amount < $merchantTransaction->amount || $voucher->currency->code !== $merchantTransaction->currency) {
                throw new NotifyErrorException(__('Voucher does not match payment requirements.'));
            }

            // Mark voucher as redeemed
            $voucher->is_active          = false;
            $voucher->redeemed_by        = $merchantTransaction->user_id   ?? null;
            $voucher->redeemed_wallet_id = $merchantTransaction->wallet_id ?? null;
            $voucher->redeemed_at        = now();
            $voucher->save();

            // Environment-aware completion description
            $completionDescription = $isSandbox
                ? __('SANDBOX: You have received a payment via Voucher from :merchant customer', ['merchant' => $merchant->business_name])
                : __('You have received a payment via Voucher from :merchant customer', ['merchant' => $merchant->business_name]);

            // Complete the transaction (use your transaction handler for consistency)
            Transaction::completeTransaction(
                $merchantTransaction->trx_id,
                null,
                $completionDescription
            );

            DB::commit();

            // Log voucher payment completion
            Log::info('Voucher payment completed', [
                'transaction_id' => $merchantTransaction->trx_id,
                'voucher_code' => $voucherCode,
                'merchant_id' => $merchant->id,
                'amount' => $merchantTransaction->amount,
                'currency' => $merchantTransaction->currency,
                'environment' => $environment->value,
                'is_sandbox' => $isSandbox,
            ]);

            notifyEvs('success', __('Voucher payment completed successfully.'));

            $redirectUrl = $trxData['success_redirect'] ?? route('status.success');

            return redirect()->to($redirectUrl);

        } catch (\Throwable $e) {
            DB::rollBack();
            
            // Log voucher payment failure with environment context
            Log::error('Voucher payment failed', [
                'transaction_id' => $merchantTransaction->trx_id,
                'voucher_code' => $voucherCode,
                'environment' => $environment->value,
                'is_sandbox' => $isSandbox,
                'error' => $e->getMessage(),
            ]);
            
            throw new NotifyErrorException($e->getMessage());
        }
    }

    /**
     * Detect environment mode from transaction data or merchant configuration.
     */
    private function detectEnvironmentMode($transaction): EnvironmentMode
    {
        // Check if environment is stored in transaction data
        if (isset($transaction->trx_data['environment'])) {
            return EnvironmentMode::from($transaction->trx_data['environment']);
        }

        // Check if is_sandbox flag is set in transaction data
        if (isset($transaction->trx_data['is_sandbox'])) {
            return $transaction->trx_data['is_sandbox'] ? EnvironmentMode::SANDBOX : EnvironmentMode::PRODUCTION;
        }

        // Fallback: Check merchant configuration
        $merchant = Merchant::find($transaction->trx_data['merchant_id']);
        if ($merchant && $merchant->sandbox_enabled) {
            // Check if transaction was created with sandbox credentials
            // This can be determined by checking transaction remarks or other indicators
            if (str_contains($transaction->remarks ?? '', 'SANDBOX_TRANSACTION')) {
                return EnvironmentMode::SANDBOX;
            }
        }

        // Default to production if no environment indicators found
        return EnvironmentMode::PRODUCTION;
    }

    /**
     * Process the merchant transaction after validations.
     */
    private function processMerchantTransaction(mixed $userWallet, mixed $merchantTransaction, mixed $user, mixed $merchant): RedirectResponse
    {
        if ($merchantTransaction->status !== TrxStatus::PENDING || $merchantTransaction->isExpired()) {
            abort(404, __('Invalid or already processed transaction.'));
        }

        if (! $userWallet) {
            return $this->handleFailure($merchantTransaction, __('A compatible UGX wallet is required.'));
        }

        // Detect environment mode
        $environment = $this->detectEnvironmentMode($merchantTransaction);
        $isSandbox = $environment->isSandbox();

        if ($user->id === $merchantTransaction->user_id) {
            return $this->handleFailure($merchantTransaction, __('You cannot pay to yourself.'), __('A payment attempt via User Wallet from :merchant customer has failed', ['merchant' => $merchant->business_name]));
        }
        $trxData        = $merchantTransaction->trx_data;
        $payableAmount  = $merchantTransaction->payable_amount;
        $merchantAmount = $merchantTransaction->amount;

        if (! Wallet::isWalletBalanceSufficient($userWallet->uuid, $payableAmount)) {
            return $this->handleFailure($merchantTransaction, __('Insufficient balance'));
        }

        $merchantWallet = Wallet::getWalletByUserId($merchantTransaction->user_id, $merchant->currency->code);

        try {
            $this->executeTransaction($userWallet, $merchantWallet, $payableAmount, $merchantAmount, $user, $merchant, $environment);

            // Environment-aware completion description
            $completionDescription = $isSandbox
                ? __('SANDBOX: You have received a payment via User Wallet from :merchant customer', ['merchant' => $merchant->business_name])
                : __('You have received a payment via User Wallet from :merchant customer', ['merchant' => $merchant->business_name]);

            Transaction::completeTransaction($merchantTransaction->trx_id, null, $completionDescription);

            notifyEvs('success', $completionDescription);

            // Log successful payment
            Log::info('Payment completed successfully', [
                'transaction_id' => $merchantTransaction->trx_id,
                'merchant_id' => $merchant->id,
                'user_id' => $user->id,
                'amount' => $payableAmount,
                'currency' => $merchant->currency->code,
                'environment' => $environment->value,
                'is_sandbox' => $isSandbox,
            ]);

            return redirect()->away($trxData['success_redirect'] ?? route('status.success'));
        } catch (Exception $e) {
            Log::error('Transaction process failed', [
                'transaction_id' => $merchantTransaction->trx_id,
                'environment' => $environment->value,
                'is_sandbox' => $isSandbox,
                'error' => $e->getMessage(),
            ]);

            return $this->handleFailure(
                $merchantTransaction,
                __('Transaction failed'),
                __('A payment attempt via User Wallet from :merchant customer has failed', ['merchant' => $merchant->business_name])
            );
        }
    }

    /**
     * Execute the wallet transaction atomically.
     *
     * @throws Throwable
     */
    private function executeTransaction(
        mixed $userWallet,
        mixed $merchantWallet,
        float $payableAmount,
        float $merchantAmount,
        mixed $user,
        mixed $merchant,
        EnvironmentMode $environment
    ): void {
        DB::transaction(function () use ($userWallet, $payableAmount, $user, $merchant, $environment) {
            $paymentHandler = app(PaymentHandler::class);

            $isSandbox = $environment->isSandbox();

            // Environment-aware transaction description
            $description = $isSandbox
                ? __('SANDBOX: Payment to :merchant via your wallet', ['merchant' => $merchant->business_name])
                : __('Payment to :merchant via your wallet', ['merchant' => $merchant->business_name]);

            $transaction = Transaction::create(new TransactionData(
                user_id: $user->id,
                trx_type: TrxType::PAYMENT,
                amount: $payableAmount,
                amount_flow: AmountFlow::MINUS,
                currency: $merchant->currency->code,
                net_amount: $payableAmount,
                payable_amount: $payableAmount,
                payable_currency: $merchant->currency->code,
                wallet_reference: $userWallet->uuid,
                description: $description,
                status: TrxStatus::COMPLETED,
                remarks: $isSandbox ? 'SANDBOX_TRANSACTION' : null
            ));

            $paymentHandler->handleSuccess($transaction);
        });
    }
}
