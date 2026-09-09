<?php

namespace App\Http\Controllers\Backend;

use App\Enums\CustomCodeType;
use App\Models\CustomCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class AppController extends BaseController
{
    public static function permissions(): array
    {
        return [
            'appInfo'                         => 'app-info',
            'styleManager|styleManagerUpdate' => 'style-manager',
            'clearCache'                      => 'app-clear-cache',
            'optimize'                        => 'app-optimize',
            'getMenusForSearch'               => 'app-info', // Allow all admin users to search menus
        ];
    }

    public function appInfo()
    {
        $data = [
            'php_version'     => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => php_uname(),
            'environment'     => app()->environment(),
            'server_ip'       => $_SERVER['SERVER_ADDR'],
            'timezone'        => config('app.timezone'),
        ];

        return view('backend.app.info', compact('data'));
    }

    public function styleManager()
    {
        $css = CustomCode::ofType(CustomCodeType::CSS);

        return view('backend.app.style_manager', compact('css'));
    }

    public function styleManagerUpdate(Request $request)
    {
        $validated = $request->validate([
            'type'    => ['required', Rule::in(CustomCodeType::values())],
            'content' => 'nullable|string',
            'status'  => 'boolean',
        ]);

        CustomCode::updateOrCreate(
            ['type' => $validated['type']],
            [
                'content' => $validated['content'],
                'status'  => $request->boolean('status'),
            ]
        );

        notifyEvs('success', __('Style Manager Updated Successfully'));

        return redirect()->back();
    }

    public function optimize()
    {
        notifyEvs('success', __('Application Optimized Successfully'));
        Artisan::call('app:optimize');

        return redirect()->back();
    }

    public function clearCache()
    {
        notifyEvs('success', __('Cache Cleared Successfully'));
        Artisan::call('app:clear');

        return redirect()->back();
    }

    public function smtpConnectionCheck(Request $request)
    {
        try {
            // Try sending a test email to the authenticated email
            Mail::raw('SMTP Test Email - Connection Successful.', function ($message) use ($request) {
                $message->to($request->input('test_email', config('mail.from.address')))
                    ->subject('SMTP Test Email');
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'SMTP connection successful. Test email sent.',
            ]);
        } catch (\Exception $e) {
            Log::error('SMTP Test Failed: '.$e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'SMTP connection failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getMenusForSearch(Request $request)
    {
        $menus = config('admin_menus');
        $flattenedMenus = [];

        foreach ($menus as $section) {
            if (isset($section['menus'])) {
                foreach ($section['menus'] as $menu) {
                    // Handle single menus
                    if ($menu['type'] === 'single' && isset($menu['route'])) {
                        try {
                            $url = route($menu['route'], $menu['params'] ?? []);
                            $flattenedMenus[] = [
                                'label' => $menu['label'],
                                'route' => $menu['route'],
                                'url' => $url,
                                'icon' => $menu['icon'] ?? 'cil-puzzle',
                                'params' => $menu['params'] ?? null,
                                'permission' => $menu['permission'] ?? null,
                                'section' => $section['label'] ?? 'General'
                            ];
                        } catch (\Exception $e) {
                            // Skip if route doesn't exist
                            continue;
                        }
                    }
                    
                    // Handle group menus with sub_menus
                    if ($menu['type'] === 'groups' && isset($menu['sub_menus'])) {
                        foreach ($menu['sub_menus'] as $subMenu) {
                            if (isset($subMenu['route'])) {
                                try {
                                    $url = route($subMenu['route'], $subMenu['params'] ?? []);
                                    $flattenedMenus[] = [
                                        'label' => $subMenu['label'],
                                        'route' => $subMenu['route'],
                                        'url' => $url,
                                        'icon' => $subMenu['icon'] ?? $menu['icon'] ?? 'setting',
                                        'params' => $subMenu['params'] ?? null,
                                        'permission' => $subMenu['permission'] ?? null,
                                        'section' => $menu['label'],
                                        'parent' => $section['label'] ?? 'General'
                                    ];
                                } catch (\Exception $e) {
                                    // Skip if route doesn't exist
                                    continue;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Filter by search query if provided
        $query = $request->get('query', '');
        if (!empty($query)) {
            $flattenedMenus = array_filter($flattenedMenus, function($menu) use ($query) {
                return stripos($menu['label'], $query) !== false || 
                       stripos($menu['section'], $query) !== false ||
                       (isset($menu['parent']) && stripos($menu['parent'], $query) !== false);
            });
        }

        // Limit results to prevent overwhelming UI
        $flattenedMenus = array_slice($flattenedMenus, 0, 10);

        return response()->json([
            'success' => true,
            'menus' => array_values($flattenedMenus)
        ]);
    }

    /**
     * Control Panel - Quick access to all admin features
     */
    public function controlPanel()
    {
        $adminPermissions = session('admin_permissions', []);
        $menus = config('admin_menus');
        $controlPanelData = [];

        foreach ($menus as $section) {
            if (isset($section['menus'])) {
                $sectionData = [
                    'label' => $section['label'] ?? 'General',
                    'features' => []
                ];

                foreach ($section['menus'] as $menu) {
                    // Handle single menu items
                    if ($menu['type'] === 'single' && isset($menu['route'])) {
                        // Check permission
                        if (isset($menu['permission']) && !in_array($menu['permission'], $adminPermissions)) {
                            continue;
                        }

                        try {
                            $url = route($menu['route'], $menu['params'] ?? []);
                            $sectionData['features'][] = [
                                'label' => $menu['label'],
                                'icon' => $menu['icon'] ?? 'cil-puzzle',
                                'url' => $url,
                                'description' => $this->getFeatureDescription($menu['label']),
                                'color' => $this->getFeatureColor($menu['label'])
                            ];
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    // Handle grouped menu items
                    if ($menu['type'] === 'groups' && isset($menu['sub_menus'])) {
                        foreach ($menu['sub_menus'] as $subMenu) {
                            if (!isset($subMenu['route'])) continue;

                            // Check permission
                            if (isset($subMenu['permission']) && !in_array($subMenu['permission'], $adminPermissions)) {
                                continue;
                            }

                            try {
                                $url = route($subMenu['route'], $subMenu['params'] ?? []);
                                $sectionData['features'][] = [
                                    'label' => $subMenu['label'],
                                    'icon' => $subMenu['icon'] ?? $menu['icon'] ?? 'cil-puzzle',
                                    'url' => $url,
                                    'description' => $this->getFeatureDescription($subMenu['label']),
                                    'color' => $this->getFeatureColor($subMenu['label']),
                                    'parent' => $menu['label']
                                ];
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }
                }

                if (!empty($sectionData['features'])) {
                    $controlPanelData[] = $sectionData;
                }
            }
        }

        return view('backend.app.control-panel', compact('controlPanelData'));
    }

    /**
     * Get feature description based on label
     */
    private function getFeatureDescription($label)
    {
        $descriptions = [
            'Dashboard' => 'Overview and analytics',
            'All Users' => 'Manage user accounts',
            'Active Users' => 'View active users',
            'Suspended Users' => 'Manage suspended accounts',
            'Unverified Users' => 'Handle unverified users',
            'KYC Unverified' => 'Process KYC verification',
            'All Merchants' => 'Manage merchants',
            'Pending Merchants' => 'Review pending merchants',
            'Approved Merchants' => 'View approved merchants',
            'Rejected Merchants' => 'Handle rejected merchants',
            'Awaiting KYC' => 'Process KYC requests',
            'KYC List' => 'View all KYC records',
            'KYC Templates' => 'Manage KYC templates',
            'Notify To Users' => 'Send notifications',
            'All Notifications' => 'View notifications',
            'Notifications Template' => 'Manage notification templates',
            'Currency Manage' => 'Configure currencies',
            'Payment Gateways' => 'Manage payment methods',
            'Virtual Card List' => 'View virtual cards',
            'CardHolders' => 'Manage cardholders',
            'Fee Settings' => 'Configure fees',
            'Provider Configuration' => 'Setup card providers',
            'Deposit History' => 'View deposits',
            'Manual Requests' => 'Handle manual requests',
            'Automatic Methods' => 'Configure auto methods',
            'Manual Methods' => 'Setup manual methods',
            'Withdraws History' => 'View withdrawals',
            'Scheduled Withdraws' => 'Manage scheduled withdrawals',
            'Transactions' => 'View all transactions',
            'Referrals' => 'Manage referral system',
            'User Ranking' => 'Configure user rankings',
            'Support Ticket' => 'Manage support tickets',
            'Support Category' => 'Organize ticket categories',
            'Site Settings' => 'Configure application',
            'Plugins Manage' => 'Manage plugins',
            'Language' => 'Multi-language settings',
            'Staff' => 'Manage admin staff',
            'Roles & Permissions' => 'Configure access control',
            'Custom Landing Page' => 'Design landing pages',
            'Navigation Manage' => 'Configure site navigation',
            'Page Manage' => 'Manage website pages',
            'Component Manage' => 'Handle page components',
            'Footer Manage' => 'Configure footer',
            'Blog' => 'Manage blog posts',
            'Category' => 'Organize blog categories',
            'Subscribers' => 'Manage email subscribers',
            'Social Links' => 'Configure social media',
            'SEO Manage' => 'Optimize for search engines',
            'Activity Log' => 'Monitor user activities',
            'Style Manager' => 'Customize application styles',
            'Optimize App' => 'Performance optimization',
            'Clear Cache App' => 'Clear application cache',
            'App Info' => 'View application information'
        ];

        return $descriptions[$label] ?? 'Administrative function';
    }

    /**
     * Get feature color scheme based on label
     */
    private function getFeatureColor($label)
    {
        $colorMap = [
            'Dashboard' => 'primary',
            'Users' => 'info',
            'Merchants' => 'success',
            'KYC' => 'warning',
            'Notification' => 'secondary',
            'Currency' => 'success',
            'Payment' => 'primary',
            'Virtual' => 'info',
            'Deposit' => 'success',
            'Withdraw' => 'danger',
            'Transaction' => 'primary',
            'Referral' => 'warning',
            'Support' => 'info',
            'Setting' => 'secondary',
            'Language' => 'primary',
            'Staff' => 'warning',
            'Role' => 'danger',
            'Page' => 'info',
            'Blog' => 'success',
            'Social' => 'primary',
            'SEO' => 'warning',
            'Activity' => 'secondary',
            'Style' => 'info',
            'Optimize' => 'success',
            'App' => 'primary'
        ];

        foreach ($colorMap as $keyword => $color) {
            if (stripos($label, $keyword) !== false) {
                return $color;
            }
        }

        return 'secondary';
    }
}
