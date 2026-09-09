<?php

use App\Console\Commands\ClearApp;
use App\Console\Commands\MakeBackendController;
use App\Console\Commands\OptimizeApp;
use App\Console\Commands\SyncUserFeatures;
use App\Http\Middleware\AuthenticateMiddleware;
use App\Http\Middleware\BlockIp;
use App\Http\Middleware\CheckUserFeature;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\DemoMode;
use App\Http\Middleware\EnsureKYCVerified;
use App\Http\Middleware\EnsureTwoFactorAuthenticated;
use App\Http\Middleware\HandleReferralLinks;
use App\Http\Middleware\LockScreen;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\MerchantApiAuth;
use App\Http\Middleware\PreventDuplicateSubmission;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecureHeaders;
use App\Http\Middleware\SetPaginationView;
use App\Http\Middleware\Translate;
use App\Http\Middleware\XSS;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use JoeDixon\Translation\Console\Commands\SynchroniseMissingTranslationKeys;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            Route::middleware(['web', 'auth:admin', 'verified', 'XSS', 'lock_screen', '2fa', 'demo'])
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'ipn/*',
        ]);
        $middleware->append([
            SecureHeaders::class,

        ]);

        // Apply EnsureFrontendRequestsAreStateful only to API routes
        $middleware->appendToGroup('api', [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->appendToGroup('web', [
            MaintenanceMode::class,
            SetPaginationView::class,
            HandleReferralLinks::class,
            Translate::class,

        ]);

        $middleware->alias([
            'auth'                 => AuthenticateMiddleware::class,
            'account.status.check' => CheckUserStatus::class,
            'merchant.auth'        => MerchantApiAuth::class,
            'kyc.verified'         => EnsureKYCVerified::class,
            'guest'                => RedirectIfAuthenticated::class,
            'role'                 => RoleMiddleware::class,
            'permission'           => PermissionMiddleware::class,
            'role_or_permission'   => RoleOrPermissionMiddleware::class,
            'XSS'                  => XSS::class,
            'block.ip'             => BlockIp::class,
            'lock_screen'          => LockScreen::class,
            '2fa'                  => EnsureTwoFactorAuthenticated::class,
            'prevent.duplicate'    => PreventDuplicateSubmission::class,
            'feature'              => CheckUserFeature::class,
            'demo'                 => DemoMode::class,

        ]);
    })
    ->withCommands([
        SynchroniseMissingTranslationKeys::class,
        ClearApp::class,
        OptimizeApp::class,
        SyncUserFeatures::class,
        MakeBackendController::class,

    ])
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            $disk   = Storage::disk('public');
            $folder = 'images/temp/'.now()->subDay()->format('Y/m/d');

            if ($disk->exists($folder)) {
                $disk->deleteDirectory($folder);
                logger()->info("Summernote temp folder deleted: {$folder}");
            } else {
                logger()->info("No summernote temp folder found for deletion: {$folder}");
            }
        })->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
