<?php

namespace App\Providers;

use App\Models\FooterSection;
use App\Models\Language;
use App\Models\Navigation;
use App\Models\Social;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->composeLanguages();
        $this->composeUserNotifications();
        $this->composeSocialLinks();
        $this->composeNavigation();
        $this->composeFooter();
        $this->composeAdminPermissions();
        $this->composeQuickFunctions();
    }

    /**
     * Share active languages with caching across needed views.
     */
    protected function composeLanguages(): void
    {
        View::composer([
            'backend.layouts.partials._header',
            'frontend.layouts.user.partials._mobile_navbar',
            'frontend.layouts.user.partials._navbar',
            'frontend.layouts.partials._language_switcher',
        ], function ($view) {
            $languages = Language::activeCached();

            $view->with('languages', $languages);
        });
    }

    /**
     * Share user unread notifications across frontend navbar views.
     */
    protected function composeUserNotifications(): void
    {
        app()->singleton('unreadNotificationsData', function () {
            $user = auth()->user();

            if (! $user) {
                return [
                    'count'         => 0,
                    'notifications' => collect(),
                ];
            }

            $allUnread = $user->unreadNotifications()->latest()->get();

            return [
                'count'         => $allUnread->count(),
                'notifications' => $allUnread,
            ];
        });

        View::composer([
            'frontend.layouts.user.partials._navbar',
            'frontend.layouts.user.partials._mobile_navbar',
        ], function ($view) {
            $notificationData = app('unreadNotificationsData');

            $view->with([
                'notifications'     => $notificationData['notifications'],
                'notificationCount' => $notificationData['count'],
            ]);
        });
    }

    protected function composeQuickFunctions()
    {

        View::composer([
            'frontend.layouts.user.partials._quick_functions',
        ], function ($view) {
            $mainCount      = 6;
            $quickLinksMain = array_slice($this->getQuickLinks(), 0, $mainCount);
            $quickLinksMore = array_slice($this->getQuickLinks(), $mainCount);
            $view->with([
                'quickLinksMain' => $quickLinksMain,
                'quickLinksMore' => $quickLinksMore,
            ]);
        });
    }

    /**
     * Share active social links in frontend parts.
     */
    protected function composeSocialLinks(): void
    {
        View::composer([
            'frontend.layouts.partials._offcanvas',
            'frontend.layouts.partials._header_top',
        ], function ($view) {
            $socials = Social::activeCached();

            $view->with('socials', $socials);
        });
    }

    protected function composeNavigation()
    {
        View::composer(['frontend.layouts.partials._menu_list'], function ($view) {
            $navigations = Navigation::activeCached();
            $view->with('navigations', $navigations);
        });
    }

    protected function composeFooter()
    {
        View::composer(['frontend.layouts.partials._footer'], function ($view) {
            $footers = FooterSection::activeCached();
            $view->with('footers', $footers);
        });
    }

    private function composeAdminPermissions()
    {
        view()->composer('backend.layouts.partials._sidebar', function ($view) {
            if (Auth::guard('admin')->check() && ! session()->has('admin_permissions')) {
                session([
                    'admin_permissions' => Auth::guard('admin')->user()->getAllPermissions()->pluck('name')->toArray(),
                ]);
            }
        });

        View::composer('backend.layouts.partials._sidebar', function ($view) {
            $admin = Auth::guard('admin')->user();

            if ($admin && ! session()->has('admin_permissions')) {
                session([
                    'admin_permissions' => $admin->getAllPermissions()->pluck('name')->toArray(),
                ]);
            }
        });
    }

    private function getQuickLinks(): array
    {
        $links = [];

        // Merchant & Virtual Card links always first if authorized
        if (auth()->user()->can('merchant')) {
            $links[] = ['Merchant', 'merchant', 'user.merchant.index'];
            $links[] = ['QR Payment', 'qrcode', 'user.merchant.qr-history'];
        }

        // Main quick links
        $mainLinks = [
            // Money Management
            ['Virtual Card', 'card', 'user.virtual-card.index'],
            ['Deposit', 'deposit', 'user.deposit.create'],
            ['Transactions', 'history', 'user.transaction.index'],
            ['Withdraw', 'withdraw', 'user.withdraw.create'],
            ['Exchange Money', 'exchange', 'user.exchange-money.create'],
            ['Wallet', 'wallet', 'user.wallet.index'],
            ['Send Money', 'send-money', 'user.send-money.create'],
            ['Request Money', 'request-money', 'user.request-money.create'],
            ['Voucher', 'voucher', 'user.voucher.my'],
            // User & Business
            ['Referrals', 'referrals', 'user.referral.index'],
            ['Support', 'support', 'user.support-ticket.index'],
        ];

        // Merge merchant links (if any) with main links
        $links = array_merge($links, $mainLinks);

        return array_map(
            fn (array $link) => [
                'title' => __($link[0]),
                'icon'  => $link[1],
                'link'  => route($link[2]),
            ],
            $links
        );
    }
}
