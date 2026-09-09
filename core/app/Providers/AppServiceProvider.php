<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Models\User;
use App\Observers\MerchantObserver;
use App\Observers\UserObserver;
use App\Payment\PaymentGatewayFactory;
use App\Services\AppConfigService;
use App\Services\CurrencyService;
use App\Services\IpInfoService;
use App\Services\PaymentService;
use App\Services\QRCodeService;
use App\Services\TransactionService;
use App\Services\WalletService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services into the service container.
     */
    public function register(): void
    {
        $this->registerServices();
        $this->bindFacades();

        // Bind AppConfigService for application-wide configuration
        $this->app->singleton(AppConfigService::class, fn ($app) => new AppConfigService);
    }

    /**
     * Register singleton services for dependency injection.
     * - Use `singleton()` for shared instances across the application.
     * - Use `bind()` if a new instance is needed for each resolve.
     */
    protected function registerServices(): void
    {
        $this->app->singleton(CurrencyService::class, fn ($app) => new CurrencyService);
        $this->app->singleton(WalletService::class, fn ($app) => new WalletService);
        $this->app->singleton(TransactionService::class, fn ($app) => new TransactionService);
        $this->app->singleton(IpInfoService::class, fn ($app) => new IpInfoService);
        $this->app->singleton(QRCodeService::class, fn ($app) => new QRCodeService);

        // Bind PaymentService with dependency injection
        $this->app->singleton(PaymentService::class, fn ($app) => new PaymentService($app->make(PaymentGatewayFactory::class)));
    }

    /**
     * Bind services with aliases for Facade support.
     * This allows accessing services statically via Facades.
     */
    protected function bindFacades(): void
    {
        $this->app->singleton('currency.service', fn ($app) => $app->make(CurrencyService::class));
        $this->app->singleton('wallet.service', fn ($app) => $app->make(WalletService::class));
        $this->app->singleton('transaction.service', fn ($app) => $app->make(TransactionService::class));
        $this->app->singleton('payment.service', fn ($app) => $app->make(PaymentService::class));
        $this->app->singleton('ifinfo.service', fn ($app) => $app->make(IpInfoService::class));
    }

    /**
     * Bootstrap application services.
     * Loads configuration settings, sets up observers, and ensures security features.
     */
    public function boot(AppConfigService $appConfigService): void
    {
        $this->ensureAppKey();
        
        // Reyco Pay Solutions - Re-enable with error handling to prevent Laravel errors
        try {
            $appConfigService->applyAppSettings();
        } catch (\Exception $e) {
            \Log::error('Failed to apply app settings: ' . $e->getMessage());
        }
        
        try {
            $appConfigService->applyMailSettings();
        } catch (\Exception $e) {
            \Log::error('Failed to apply mail settings: ' . $e->getMessage());
        }
        
        try {
            $appConfigService->forceHttpsIfEnabled();
        } catch (\Exception $e) {
            \Log::error('Failed to force HTTPS: ' . $e->getMessage());
        }
        
        try {
            $appConfigService->applySmsConfig();
        } catch (\Exception $e) {
            \Log::error('Failed to apply SMS config: ' . $e->getMessage());
        }
        
        try {
            $appConfigService->applyGoogleReCaptchaConfig();
        } catch (\Exception $e) {
            \Log::error('Failed to apply Google ReCaptcha config: ' . $e->getMessage());
        }
        
        try {
            $appConfigService->ensureStorageSymlink();
        } catch (\Exception $e) {
            \Log::error('Failed to ensure storage symlink: ' . $e->getMessage());
        }
        
        $this->configureObservers();

        Application::macro('getDefaultLocale', function () {
            return config('app.default_language');
        });
    }

    /**
     * Ensure the application key is set.
     * If no application key exists, generate a new one during deployment.
     */
    protected function ensureAppKey(): void
    {
        if (config('app.key') === '') {
            Artisan::call('key:generate', ['--force' => true]);
            Log::info('Application key generated successfully during deployment.');
        }
    }

    /**
     * Register model observers to handle model events.
     */
    protected function configureObservers(): void
    {
        User::observe(UserObserver::class);
        Merchant::observe(MerchantObserver::class);
    }
}
