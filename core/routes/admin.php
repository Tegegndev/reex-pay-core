<?php

use App\Http\Controllers\Backend\ActivityController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\AppController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\CardholdersController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\CustomLandingController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DepositController;
use App\Http\Controllers\Backend\DepositMethodController;
use App\Http\Controllers\Backend\FooterItemController;
use App\Http\Controllers\Backend\FooterSectionController;
use App\Http\Controllers\Backend\KycController;
use App\Http\Controllers\Backend\KycTemplateController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\MerchantController;
use App\Http\Controllers\Backend\NavigationController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\NotificationTemplateController;
use App\Http\Controllers\Backend\PageComponentController;
use App\Http\Controllers\Backend\PageComponentRepeatedContentController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\PaymentGatewayController;
use App\Http\Controllers\Backend\PluginController;
use App\Http\Controllers\Backend\ReferralController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SiteSeoController;
use App\Http\Controllers\Backend\SocialController;
use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\SubscriberController;
use App\Http\Controllers\Backend\SupportCategoryController;
use App\Http\Controllers\Backend\TicketController;
use App\Http\Controllers\Backend\TransactionController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\UserManageController;
use App\Http\Controllers\Backend\UserRankController;
use App\Http\Controllers\Backend\VirtualCardController;
use App\Http\Controllers\Backend\VirtualCardFeeSettingController;
use App\Http\Controllers\Backend\WithdrawController;
use App\Http\Controllers\Backend\WithdrawMethodController;
use App\Http\Controllers\Backend\WithdrawScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin/Backend Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the bootstrap/app within a group which
| contains the "web,admin" middleware group. Now create something great!
|
*/

Route::prefix(setting('admin_prefix'))->as('admin.')->group(function () {

    // ========================== 🌟 Dashboard =============================
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ========================== 👥 User Management ==========================
    Route::prefix('user')->as('user.')->controller(UserManageController::class)->group(function () {

        // 🔹 User Actions (GET)
        Route::get('manage/{username}/{param?}', 'manageUser')->name('manage');
        Route::get('login/{id}', 'loginAsUser')->name('login');
        Route::get('mail-send/all', 'mailSendAll')->name('mail-send.all');

        // 🔹 User Updates (POST)
        Route::post('feature-status/update', 'updateFeatureStatus')->name('feature-status.update');
        Route::post('update-balance', 'updateBalance')->name('update-balance');
        Route::post('status-update/{id}', 'statusUpdate')->name('status-update');
        Route::post('password-update/{id}', 'passwordUpdate')->name('password-update');
        Route::post('mail-send', 'mailSend')->name('mail-send');

        // 🔹 User Info Update (PUT)
        Route::put('update-info/{id}', 'infoUpdate')->name('update-info');
    });

    // 🔹 User Listings (GET)
    Route::prefix('user')->as('user.')->controller(UserController::class)->group(function () {
        Route::get('active', 'activeUser')->name('active');
        Route::get('suspended', 'suspendedUser')->name('suspended');
        Route::get('unverified', 'unverifiedUser')->name('unverified');
        Route::get('kyc-unverified', 'kycUnverifiedUser')->name('kyc-unverified');
        Route::get('{id}/transaction-stats', 'transactionStats')->name('transaction-stats');
        Route::post('{id}/convert-to-merchant', 'convertToMerchant')->name('convert-to-merchant');
    });
    // 🔹 User Resources
    Route::resource('user', UserController::class)->except(['show', 'create', 'edit']);

    // =============================== 🏪 Merchant Management  =================================
    Route::prefix('merchant')->as('merchant.')->controller(MerchantController::class)->group(function () {
        Route::get('pending', 'pendingMerchant')->name('pending');
        Route::get('approved', 'approvedMerchant')->name('approved');
        Route::get('rejected', 'rejectedMerchant')->name('rejected');
        Route::post('request-action', 'merchantAction')->name('request-action');
    });
    // 🔹 Merchant Resources
    Route::resource('merchant', MerchantController::class);

    // ================================ 🔑 KYC Management   =================================
    Route::prefix('kyc')->as('kyc.')->group(function () {
        Route::controller(KycController::class)->group(function () {
            Route::get('pending', 'pending')->name('pending');
            Route::get('index', 'index')->name('index');
            Route::post('action', 'requestAction')->name('request-action');
        });
        Route::resource('template', KycTemplateController::class)->except(['show', 'create']);
    });

    // ================================ 🔍 User/Merchant Activity History  =================================
    Route::get('activity-log', [ActivityController::class, 'index'])->name('activity-log');

    // ================================ 🔐 Admin Profile  =================================
    Route::prefix('profile')->as('profile.')->controller(AdminController::class)->group(function () {
        Route::get('profile', 'profile')->name('view');
        Route::post('info-update', 'updateInfo')->name('info.update');
        Route::post('password-update', 'updatePassword')->name('password.update');

        // Two-Factor Authentication
        Route::prefix('2fa')->as('2fa.')->group(function () {
            Route::post('enable', 'enable2fa')->name('enable');
            Route::post('disable', 'disable2fa')->name('disable');
        });
    });

    // ======================== 👨‍💼 Staff Management  ==============================
    Route::resource('staff', StaffController::class)->except(['show', 'create', 'destroy']);
    Route::resource('role', RoleController::class);

    // ======================== 💱 Currency Management  ==============================
    Route::resource('currency', CurrencyController::class);

    // ================================== 💳 Payment Gateway ===============================
    Route::prefix('payment')->as('payment.')->group(function () {
        Route::resource('gateway', PaymentGatewayController::class)->only(['index', 'edit', 'update']);
        Route::get('gateway-currency/{gateway_id}', [PaymentGatewayController::class, 'gatewayCurrency'])->name('gateway-currency');
    });

    // ======================== 💳 Virtual Card Management  ===============================
    Route::prefix('virtual-card')->name('virtual-card.')->controller(VirtualCardController::class)->group(function () {
        // Card Requests
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('awaiting', 'requestAwaiting')->name('awaiting');
            Route::get('all', 'requestAll')->name('all');
            Route::post('{uuid}/review', 'review')->name('review');
        });

        // Cardholder management routes
        Route::prefix('cardholders')->name('cardholders.')->controller(CardholdersController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}/action', 'action')->name('action');
        });

        // Card Management
        Route::get('list', 'virtualCardList')->name('list');
        Route::post('update-status', 'statusUpdate')->name('update-status');

        // Provider Configuration
        Route::prefix('provider')->name('provider.')->group(function () {
            Route::get('/', 'provider')->name('index');
            Route::get('manage/{id}', 'providerManage')->name('manage');
            Route::put('update/{provider}', 'providerUpdate')->name('update');
        });

        // Virtual Card Settings
        Route::resource('fee-settings', VirtualCardFeeSettingController::class)
            ->names('fee-settings');
    });

    // ======================== 💰 Deposit Management  ===============================
    Route::prefix('deposit')->as('deposit.')->group(function () {
        Route::controller(DepositController::class)->group(function () {
            Route::get('manual-request', 'manualRequest')->name('manual-request');
            Route::get('history', 'history')->name('history');
            Route::post('request-action', 'requestAction')->name('request-action');
        });
        Route::resource('method', DepositMethodController::class)->except('show');
    });

    // ======================== 🏦 Withdraw Management   ===============================
    Route::prefix('withdraw')->as('withdraw.')->group(function () {
        Route::controller(WithdrawController::class)->group(function () {
            Route::get('manual-request', 'manualRequest')->name('manual-request');
            Route::get('history', 'history')->name('history');
            Route::post('request-action', 'requestAction')->name('request-action');
        });
        Route::resource('method', WithdrawMethodController::class)->except('show');
        Route::controller(WithdrawScheduleController::class)->group(function () {
            Route::get('schedule', 'index')->name('schedule');
            Route::post('schedule-update', 'update')->name('schedule.update');
        });
    });

    // ======================== 🏆 Referral Management   ===============================
    Route::prefix('referral')->as('referral.')->group(function () {
        Route::get('index', [ReferralController::class, 'index'])->name('index');
        Route::post('store', [ReferralController::class, 'store'])->name('store');
        Route::get('edit/{id}', [ReferralController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [ReferralController::class, 'update'])->name('update');
        Route::get('status-update/{type}/{status}', [ReferralController::class, 'statusUpdate'])->name('status-update');
        Route::delete('delete/{id}', [ReferralController::class, 'destroy'])->name('delete');
        Route::get('card-content', [ReferralController::class, 'cardContent'])->name('card.content');
        Route::post('content-update', [ReferralController::class, 'contentUpdate'])->name('content.update');
    });

    // ======================== User Ranking Management   ===============================
    Route::resource('ranking', UserRankController::class)->except(['create', 'show', 'destroy']);

    // ======================== 🔄 Transaction Management  ===============================
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction');

    // ======================== ⚙️ Site Management  ==============================
    Route::prefix('settings')->as('settings.')->group(function () {
        Route::resource('site', SettingController::class)->only(['index', 'update']);
        Route::resource('plugin', PluginController::class)->only(['index', 'edit', 'update']);
        Route::get('{plugin_type}', [PluginController::class, 'pluginType'])->name('plugin_type');
    });

    // ======================== 🎫 Support Ticket  ==============================
    Route::prefix('support-ticket')->as('support-ticket.')->controller(TicketController::class)->group(function () {
        Route::resource('category', SupportCategoryController::class)->except(['show', 'create']);
        Route::get('pending', 'pendingTicket')->name('new');
        Route::get('inprogress', 'inprogress')->name('inprogress');
        Route::get('close', 'closeTicket')->name('close');
        Route::get('history', 'history')->name('history');
        Route::get('show/{ticket}', 'ticketShow')->name('show');
        Route::post('reply/{ticket}', 'ticketReplyStore')->name('reply');
        Route::put('status-update/{ticket_id}', 'statusUpdate')->name('status-update');
    });

    //  ️️======================== 🔔 Notification Management  ==============================
    Route::prefix('notifications')->name('notifications.')->group(function () {

        // 🔹 Notification management routes (prefix: notification)
        Route::controller(NotificationController::class)->group(function () {
            // 🔹 Admin-triggered user notification
            Route::get('to-users', 'notifyUsers')->name('notifyToUser');
            Route::post('to-users/send', 'sendNotification')->name('notifyToUser.send');

            // 🔹 Display notifications
            Route::get('/', 'index')->name('index');
            Route::get('/recent', 'recent')->name('recent');

            // 🔹 State-changing actions (use PATCH)
            Route::get('/{notification}/read', 'markAsRead')->name('markAsRead');
            Route::get('/read-all', 'markAllAsRead')->name('markAllAsRead');
        });

        // 🔹 Template management routes (prefix: template)
        Route::prefix('template')->name('template.')->controller(NotificationTemplateController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{template}/edit', 'edit')->name('edit');
            Route::put('{template}/channel/{channel}', 'updateChannel')->name('update');
        });

    });

    // ======================= 🌎 Language Management =======================
    Route::prefix('language')->name('language.')->controller(LanguageController::class)->group(function () {
        Route::get('translate/{code}', 'translate')->name('translate');
        Route::post('translate-update', 'translatedUpdate')->name('translate-update');
        Route::get('sync-missing-keys', 'syncMissingKeys')->name('sync-missing-keys');
    });
    // 🔹 Resource Language CRUD
    Route::resource('language', LanguageController::class);

    // ======================= 🎉 Custom Landing Page =======================
    Route::resource('custom-landing', CustomLandingController::class);
    Route::prefix('custom-landing')->name('custom-landing.')->controller(CustomLandingController::class)->group(function () {
        Route::get('{id}/manage-html', 'manageHtml')->name('manage-html');
        Route::post('{id}/manage-html-update', 'manageHtmlUpdate')->name('manage-html-update')->withoutMiddleware('XSS');
    });

    // ======================= 🏷️ Navigation Management =======================
    Route::prefix('navigation')->as('navigation.')->controller(NavigationController::class)->group(function () {
        Route::resource('site', NavigationController::class)->except(['create', 'show']);
        Route::post('position-update', 'positionUpdate')->name('position-update');
    });

    // ======================= 📄 Page Management =======================
    Route::prefix('page')->as('page.')->group(function () {
        Route::resource('site', PageController::class)->except('show');
        Route::resource('component', PageComponentController::class)->except('show')->withoutMiddleware('XSS');
        Route::resource('component-repeated-content', PageComponentRepeatedContentController::class)->only(['edit', 'store', 'update', 'destroy']);

        // 🔹 Page Footer
        Route::prefix('footer')->as('footer.')->group(function () {

            // Footer Section Routes
            Route::resource('section', FooterSectionController::class)->except(['show', 'create']);
            Route::post('section/position-update', [FooterSectionController::class, 'positionUpdate'])->name('section.position-update');

            // Footer Item Routes
            Route::resource('item', FooterItemController::class)->except(['show', 'create']);
            Route::post('item/position-update', [FooterItemController::class, 'positionUpdate'])->name('item.position-update');

        });
    });

    // ======================= 📰 Blog Management =======================
    Route::prefix('blog')->as('blog.')->group(function () {
        Route::resource('post', BlogController::class)->withoutMiddleware('XSS');
        Route::resource('category', BlogCategoryController::class);
    });

    // ======================== 📱 Social Management ========================
    Route::resource('social', SocialController::class)->except(['create', 'show']);

    // ======================= 🔍 Site SEO Management =======================
    Route::resource('site-seo', SiteSeoController::class)->except(['show']);

    // ======================= 📧 Subscriber Management =======================
    Route::prefix('subscriber')->as('subscriber.')->controller(SubscriberController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('send-mail', 'sendMail')->name('send-mail');
        Route::delete('delete/{id}', 'deleteSubscriber')->name('delete');
    });

    // ======================= 🚀 Application Tools =======================
    Route::prefix('app')->as('app.')->controller(AppController::class)->group(function () {
        Route::get('/', 'appInfo')->name('info');
        Route::get('/control-panel', 'controlPanel')->name('control-panel');
        Route::get('style-manager', 'styleManager')->name('style-manager');
        Route::post('style-manager', 'styleManagerUpdate')->name('style-manager-update');

        Route::get('/optimize', 'optimize')->name('optimize');
        Route::get('/clear-cache', 'clearCache')->name('clear-cache');
        Route::post('/smtp-connection-check', 'smtpConnectionCheck')->name('smtp-connection-check');
        
        // Menu search functionality
        Route::get('menu-search', 'getMenusForSearch')->name('menu-search');
    });

});
