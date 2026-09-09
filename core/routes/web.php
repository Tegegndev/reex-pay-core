<?php

use App\Http\Controllers\Api\ApiDocsController;
use App\Http\Controllers\Common\AppController;
use App\Http\Controllers\Common\FileController;
use App\Http\Controllers\Common\LocaleController;
use App\Http\Controllers\Common\SummernoteController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\CardholdersController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\DepositController;
use App\Http\Controllers\Frontend\ExchangeMoneyController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\IPNController;
use App\Http\Controllers\Frontend\KycSubmissionController;
use App\Http\Controllers\Frontend\MerchantController;
use App\Http\Controllers\Frontend\MerchantPaymentReceiveController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ReferralController;
use App\Http\Controllers\Frontend\RequestMoneyController;
use App\Http\Controllers\Frontend\SendMoneyController;
use App\Http\Controllers\Frontend\SettingController;
use App\Http\Controllers\Frontend\StatusController;
use App\Http\Controllers\Frontend\SubscriberController;
use App\Http\Controllers\Frontend\TicketController;
use App\Http\Controllers\Frontend\TransactionController;
use App\Http\Controllers\Frontend\TwoFactorController;
use App\Http\Controllers\Frontend\UserRankController;
use App\Http\Controllers\Frontend\VirtualCardController;
use App\Http\Controllers\Frontend\VirtualCardRequestController;
use App\Http\Controllers\Frontend\VoucherController;
use App\Http\Controllers\Frontend\WalletController;
use App\Http\Controllers\Frontend\WithdrawAccountController;
use App\Http\Controllers\Frontend\WithdrawController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page Routes
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');

// Redirect /home to /
Route::redirect('/home', '/');

// Blog Routes
Route::prefix('blog')->as('blog.')->controller(BlogController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'details')->name('details');
});

// Contact Routes
Route::post('/contact-submit', [ContactController::class, 'submit'])->name('contact.submit');

// Subscribe
Route::post('/subscribe', SubscriberController::class)->name('subscribe.submit');

/*
|--------------------------------------------------------------------------
| All Type User Routes Like Normal User, Merchant User
|--------------------------------------------------------------------------
*/
Route::prefix('user')->as('user.')->middleware(['auth', 'account.status.check', 'verified', '2fa', 'block.ip'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ========================== User Settings Routes =============================
    Route::prefix('settings')->as('settings.')->controller(SettingController::class)->group(function () {
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile-update', 'profileUpdate')->name('profile.update');
        Route::get('change-password', 'changePassword')->name('password.change');
        Route::post('password-update', 'passwordUpdate')->name('password.update');
        Route::get('verify-email', 'verifyEmail')->name('verify-email');

        // Two-Factor Authentication
        Route::prefix('2fa')->as('2fa.')->controller(TwoFactorController::class)->group(function () {
            Route::get('setup', 'showSetupForm')->name('setup');
            Route::post('enable', 'enable2fa')->name('enable');
            Route::post('disable', 'disable2fa')->name('disable');
        });

        // KYC Verification
        Route::prefix('kyc')->as('kyc.')->controller(KycSubmissionController::class)->group(function () {
            Route::get('verify', 'kycVerify')->name('verify');
            Route::get('template/details/{id}', 'templateDetails')->name('template.details');

            Route::post('submit', 'kycSubmit')->name('submit');
        });
    });

    // ========================== Wallet Routes =============================
    Route::prefix('wallet')->as('wallet.')->controller(WalletController::class)->group(function () {
        Route::get('list', 'index')->name('index');
        Route::post('create', 'create')->name('create');
        Route::get('currency-info/{currency_id}', 'currencyInfo')->name('currency-info');
        Route::post('status', 'status')->name('status');

        // json response
        Route::get('supported-payment-methods/{wallet_id}', 'supportedPaymentMethods')->name('supported-payment-methods');
        Route::get('info/{role}/{wallet_id}', 'getWalletInfo')->name('info');
        Route::get('validate-recipient/{role}/{emailOrWalletId}', 'validateRecipient')->name('validate.recipient');
    });

    // ========================== Deposit Money Routes =============================
    Route::prefix('deposit')->as('deposit.')->controller(DepositController::class)->middleware(['prevent.duplicate'])->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store')->middleware(['feature:deposit', 'throttle:10,1']);
        Route::get('credentials/{method_id}', 'credentials')->name('credentials');
        Route::get('history', 'history')->name('history');
    });

    // ========================== Transfer/Send Money Routes =============================
    Route::prefix('send-money')->as('send-money.')->controller(SendMoneyController::class)->middleware(['prevent.duplicate'])->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store')->middleware(['kyc.verified', 'feature:send_money']);
    });

    // ========================== Money Request Routes =============================
    Route::prefix('request-money')->as('request-money.')->controller(RequestMoneyController::class)->middleware(['prevent.duplicate'])->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store')->middleware(['kyc.verified', 'feature:request_money']);
    });

    // ========================== Exchange Money Routes =============================
    Route::prefix('exchange-money')->as('exchange-money.')->controller(ExchangeMoneyController::class)->middleware(['prevent.duplicate'])->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store')->middleware(['feature:exchange_money']);
    });

    // ========================== Virtual Card Routes =============================
    Route::prefix('virtual-card')->as('virtual-card.')->group(function () {

        // Cardholders Management
        Route::resource('cardholders', CardholdersController::class)->names('cardholders');

        // Virtual Card Request
        Route::prefix('request')->as('request.')->controller(VirtualCardRequestController::class)->group(function () {
            Route::get('index', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::get('eligible-wallets', 'eligibleWallets')->name('eligible-wallets'); // JSON response for virtual card eligibility check
        });

        // My Virtual Card
        Route::controller(VirtualCardController::class)->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('card-details/{id}/{provider}', 'cardDetails')->name('card-details');

            Route::get('topup/{card}', 'topup')->name('topup');
            Route::post('topup-store', 'topupStore')->name('topup-store');

            Route::get('withdraw/{card}', 'withdraw')->name('withdraw');
            Route::post('withdraw-store', 'withdrawStore')->name('withdraw-store');

        });
    });

    // =========================== Voucher Routes =============================
    Route::prefix('voucher')->as('voucher.')->controller(VoucherController::class)->group(function () {
        Route::get('my', 'myVouchers')->name('my');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::post('redeem', 'redeem')->name('redeem');
    });

    // ========================== Withdraw Money Routes =============================
    Route::prefix('withdraw')->as('withdraw.')->controller(WithdrawController::class)->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store')->middleware(['kyc.verified', 'prevent.duplicate', 'feature:withdraw', 'throttle:5,1']);
        Route::get('credentials-fields/{method_id}', 'credentialsFields')->name('credentials.fields');
        Route::get('account-info/{id}', [WithdrawAccountController::class, 'accountInfo'])->name('account.info');
        Route::resource('account', WithdrawAccountController::class)->except(['show', 'destroy']);
    });

    // ========================== Support Management Routes =============================
    Route::prefix('support-ticket')->as('support-ticket.')->controller(TicketController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('show/{ticket}', 'show')->name('show');
        Route::post('reply/{ticket}', 'reply')->name('reply');
        Route::get('close/{ticket}', 'close')->name('close');
    });

    // ========================== Transaction Routes =============================
    Route::prefix('transaction')->as('transaction.')->controller(TransactionController::class)->group(function () {
        Route::get('index', [TransactionController::class, 'index'])->name('index');
        Route::get('download-pdf/{trx_id}', [TransactionController::class, 'downloadPdf'])->name('download-pdf');
        Route::post('action', [TransactionController::class, 'handleAction'])->name('action');
    });

    // ========================== Referral Routes =============================
    Route::prefix('referral')->as('referral.')->controller(ReferralController::class)->group(function () {
        Route::get('index', 'index')->name('index');
    });

    // ========================== User Rank Routes =============================
    Route::get('rank-showcase', [UserRankController::class, 'showcase'])->name('rank.showcase');

    // ========================== Merchant Routes =============================
    Route::middleware('can:merchant')->group(function () {
        Route::resource('merchant', MerchantController::class)->except(['show', 'destroy']);
        Route::get('merchant/{merchant}/config', [MerchantController::class, 'merchantConfig'])->name('merchant.config');
        Route::post('merchant/switch-environment', [MerchantController::class, 'switchEnvironment'])->name('merchant.switch-environment');

        // QR Payment
        Route::get('{merchant}/qr-payment', [MerchantController::class, 'showQrForm'])->name('merchant.qr-payment');
        Route::post('{merchant}/qr-generate', [MerchantController::class, 'generateQr'])->name('merchant.qr-generate');
        Route::get('merchant-qr-history', [MerchantController::class, 'qrHistory'])->name('merchant.qr-history');

    });

    // Notification Management Routes
    Route::controller(NotificationController::class)->prefix('notifications')->as('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('recent', 'recent')->name('recent');

        Route::get('{id}/read', 'markAsRead')->name('markAsRead');
        Route::get('read-all', 'markAllAsRead')->name('read-all');
    });
});

/*
|--------------------------------------------------------------------------
| Instant Payment Notification (IPN)
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/ipn/{gateway}', [IPNController::class, 'handleIPN'])
    ->middleware('throttle:120,1')
    ->name('ipn.handle');

// Payment Status Routes
Route::prefix('status')->as('status.')->controller(StatusController::class)->group(function () {
    Route::match(['get', 'post'], 'success', 'success')->name('success');
    Route::match(['get', 'post'], 'cancel', 'cancel')->name('cancel');
    Route::match(['get', 'post'], 'pending', 'pending')->name('pending');
    Route::match(['get', 'post'], 'callback', 'callback')->name('callback');
});

// ========================== Merchant Payment Routes =============================
Route::prefix('payment')->as('payment.')->controller(MerchantPaymentReceiveController::class)->group(function () {
    Route::get('checkout', 'paymentCheckoutSigned')->name('checkout')->middleware('signed');
    Route::get('pay/{merchant}/{token}', 'paymentCheckoutPublic')->name('pay');
    Route::post('process', 'processPayment')->middleware('throttle:20,1')->name('process');
    Route::get('wallet-pay/{token}', 'walletPayment')->name('wallet.pay');
    Route::post('complete', 'completePayment')->middleware('throttle:10,1')->name('complete');
    Route::match(['get', 'post'], 'with-account', 'payWithAccount')->name('with.account')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/
Route::get('locale-set/{locale}', [LocaleController::class, 'setLocale'])->name('locale-set');
// Get currency rate with JSON response
Route::get('currency-rate/{fromCurrency}/{toCurrency}', [AppController::class, 'getCurrencyRate'])->name('get-currency-rate');
// Download File
Route::get('/file/download/{filePath}', [FileController::class, 'download'])->where('filePath', '.*')->name('file.download');

Route::prefix('summernote')->as('summernote.')->controller(SummernoteController::class)->group(function () {
    Route::post('image-upload', 'imageUpload')->name('image-upload');
    Route::post('image-delete', 'imageDelete')->name('image-delete');
});

/*
|--------------------------------------------------------------------------
| Merchant Api Documentation
|--------------------------------------------------------------------------
*/
Route::prefix('api-docs')->as('api-docs.')->group(function () {
    Route::get('/', [ApiDocsController::class, 'index'])->name('index');
});

/*
|--------------------------------------------------------------------------
| LAST: CMS Dynamic Page (Slug-Based)
|--------------------------------------------------------------------------
*/

Route::get('{slug}', PageController::class)
    ->where('slug', '^(?!admin|user|merchant|api|dashboard|payment|login|register).*$')
    ->name('page.view');
