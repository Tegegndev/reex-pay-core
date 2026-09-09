<?php

namespace App\Http\Controllers\Frontend;

use App\Data\TransactionData;
use App\Enums\AmountFlow;
use App\Enums\EnvironmentMode;
use App\Enums\MerchantStatus;
use App\Enums\TrxStatus;
use App\Enums\TrxType;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Merchant;
use App\Models\Transaction as TransactionModel;
use App\Notifications\TemplateNotification;
use App\Services\QRCodeService;
use App\Traits\FileManageTrait;
use Currency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Transaction;
use Wallet;

class MerchantController extends Controller
{
    use FileManageTrait,AuthorizesRequests;

    protected QRCodeService $qrCodeService;

    /**
     * Inject QRCodeService (singleton) via constructor.
     */
    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index()
    {
        $merchants = Merchant::where('user_id', auth()->id())->get();

        return view('frontend.user.merchant.index', compact('merchants'));
    }

    public function create()
    {
        $currencies = auth()->user()->activeWallets()->pluck('currency');

        return view('frontend.user.merchant.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name'        => 'required|string|max:255',
            'site_url'             => 'required|url',
            'currency_id'          => 'required|exists:currencies,id',
            'business_logo'        => 'nullable|image|max:1024',
            'business_email'       => 'nullable|email|max:255',
            'business_description' => 'nullable|string|max:500',
        ]);

        // Attach the authenticated user's ID
        $validated['user_id'] = auth()->id();

        // Handle file upload if a business logo is provided
        if ($request->hasFile('business_logo')) {
            $validated['business_logo'] = $this->uploadImage($request->file('business_logo'));
        }

        // Use mass assignment to create the merchant
        Merchant::create($validated);

        // Notify admin
        $admins = Admin::permission('merchant-request-notification')->get();
        Notification::send($admins, new TemplateNotification(
            identifier: 'merchant_admin_notify_shop_request',
            data: [
                'user'           => auth()->user()->name,
                'business_name'  => $validated['business_name'],
                'business_email' => $validated['business_email'],
                'site_url'       => $validated['site_url'],
            ],
            sender: auth()->user(),
            action: route('admin.merchant.pending')
        ));

        // Send a success notification
        notifyEvs('success', __('New Merchant Request Successfully'));

        // Redirect to merchant index
        return to_route('user.merchant.index');
    }

    public function edit($id)
    {
        $currencies = auth()->user()->activeWallets()->pluck('currency');
        $merchant   = Merchant::findOrFail($id);

        return view('frontend.user.merchant.edit', compact('merchant', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $merchant = Merchant::findOrFail($id);

        $validated = $request->validate([
            'business_name'        => 'required|string|max:255',
            'site_url'             => 'required|url',
            'currency_id'          => 'required|exists:currencies,id',
            'business_logo'        => 'nullable|image|max:1024',
            'business_email'       => 'nullable|email|max:255',
            'business_description' => 'nullable|string|max:500',
        ]);

        // Check if currency has changed
        if ($merchant->currency_id !== (int) $validated['currency_id']) {
            $validated['status'] = MerchantStatus::PENDING;
        }

        // Handle file upload if a new business logo is provided
        if ($request->hasFile('business_logo')) {

            $validated['business_logo'] = $this->uploadImage($request->file('business_logo'), $merchant->business_logo);
        }

        // Update the merchant with validated data
        $merchant->update($validated);

        // Send a success notification
        notifyEvs('success', __('Merchant details updated successfully'));

        // Redirect to the merchant index page
        return to_route('user.merchant.index');
    }

    public function merchantConfig(Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        return view('frontend.user.merchant.config', compact('merchant'));
    }

    /**
     * Switch merchant environment between sandbox and production
     */
    public function switchEnvironment(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'environment' => 'required|in:sandbox,production'
        ]);

        $merchant = Merchant::findOrFail($validated['merchant_id']);
        
        // Ensure user owns this merchant
        $this->authorize('update', $merchant);

        try {
            $environmentEnum = EnvironmentMode::from($validated['environment']);
            
            // Generate test credentials if switching to sandbox and they don't exist
            if ($environmentEnum->isSandbox() && !$merchant->hasTestCredentials()) {
                $merchant->generateTestCredentials();
            }

            // Switch environment using enum
            if ($environmentEnum->isSandbox()) {
                $success = $merchant->switchToSandbox();
            } else {
                $success = $merchant->switchToProduction();
            }

            if ($success) {
                $message = $environmentEnum->isSandbox()
                    ? __('Successfully switched to sandbox mode. You can now test your API integration safely.')
                    : __('Successfully switched to production mode. Your API will now process live transactions.');

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'current_mode' => $merchant->current_mode->value
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to switch environment. Please try again.')
                ], 400);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Environment switch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while switching environment.')
            ], 500);
        }
    }

    public function showQrForm(Merchant $merchant, Request $request)
    {
        $qrCode        = null;
        $paymentUrl    = null;
        $paymentAmount = null;

        $qrToken = $request->input('qr_token');

        if ($qrToken) {
            // 1. Retrieve the transaction using the custom scope
            $transaction = TransactionModel::byTrxToken($qrToken)->first();

            // 2. Ensure transaction is valid
            if ($transaction && $transaction->status === TrxStatus::PENDING && ! $transaction->isExpired()) {
                // Generate payment URL and QR code
                $paymentUrl = route('payment.pay', [
                    'merchant' => Str::slug($merchant->business_name),
                    'token'    => $qrToken,
                ]);

                $qrCode = $this->qrCodeService->generate($paymentUrl);

                // Prepare payment data
                $paymentAmount = $transaction->payable_amount.' '.$transaction->payable_currency;
            } else {
                notifyEvs('error', __('Invalid or expired transaction.'));

                return redirect()->route('user.merchant.qr-payment', ['merchant' => $merchant->id]);
            }
        }

        return view('frontend.user.merchant.qr_payment', compact('merchant', 'qrCode', 'paymentUrl', 'paymentAmount'));
    }

    public function generateQr(Request $request, Merchant $merchant)
    {
        // Validate request
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'currency'    => 'required|string',
            'expire_time' => 'nullable|numeric|min:0',
            'note'        => 'nullable|string',
        ]);

        $currencyCode = $validated['currency'];

        // Currency checks
        if (! Currency::where('code', $currencyCode)->where('status', true)->exists()) {
            notifyEvs('error', __('Invalid currency code.'));

            return back();
        }

        if ($merchant->currency->code !== $currencyCode) {
            notifyEvs('error', __('Currency code mismatch.'));

            return back();
        }

        $amount             = $validated['amount'];
        $note               = $validated['note'];
        $merchantFeePercent = $merchant->fee;
        $fee                = $amount * $merchantFeePercent / 100;
        $netAmount          = $amount - $fee;

        $userId         = auth()->id();
        $merchantWallet = Wallet::getWalletByUserId($userId, $currencyCode);
        if (! $merchantWallet) {
            notifyEvs('error', __('An active :currency wallet is required to create a payment link.', ['currency' => $currencyCode]));

            return back();
        }
        $description    = __('Payment for :business', [
            'business' => $merchant->business_name,
        ]);

        $paymentData = [
            'merchant_id'   => $merchant->id,
            'merchant_name' => $merchant->business_name,
            'amount'        => $amount,
            'currency_code' => $currencyCode,
            'description'   => $note ?? $description,
        ];

        $expiresInMinutes = isset($validated['expire_time']) ? (int) $validated['expire_time'] : 30;
        $expiresAt        = $expiresInMinutes > 0 ? now()->addMinutes($expiresInMinutes) : null;

        DB::beginTransaction();

        try {
            // Generate a unique short token
            do {
                $trxToken = Str::random(16);
            } while (TransactionModel::byTrxToken($trxToken)->exists());

            Transaction::create(new TransactionData(
                user_id: $merchant->user->id,
                trx_type: TrxType::RECEIVE_PAYMENT,
                amount: $netAmount,
                amount_flow: AmountFlow::PLUS,
                fee: $fee,
                currency: $currencyCode,
                net_amount: $netAmount,
                payable_amount: $amount,
                payable_currency: $currencyCode,
                wallet_reference: $merchantWallet->uuid,
                trx_data: $paymentData,
                description: $description,
                trx_token: $trxToken,
                expires_at: $expiresAt,
            ));
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            notifyEvs('error', __('Failed to create transaction.'));

            return back();
        }

        return redirect()->route('user.merchant.qr-payment', [
            'merchant' => $merchant->id,
            'qr_token' => $trxToken,
        ]);

    }

    public function qrHistory()
    {

        $qrTransactions = TransactionModel::query()
            ->where('trx_type', TrxType::RECEIVE_PAYMENT)
            ->where('trx_token', '!=', null)
            ->where('user_id', auth()->id())
            ->latest()->paginate(6);

        return view('frontend.user.merchant.qr_history', compact('qrTransactions'));
    }
}
