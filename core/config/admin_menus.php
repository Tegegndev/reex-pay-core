<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sidebar Menu Items
    |--------------------------------------------------------------------------
    |
    | Here you can change the sidebar menu items
    |
    */

    [
        'menus' => [
            [
                'label' => 'Dashboard',
                'icon'  => 'cil-speedometer',
                'type'  => 'single',
                'route' => 'admin.dashboard',
            ],
        ],
    ],
    [
        'label' => 'Account Management',
        'menus' => [
            [
                'label'     => 'Users',
                'icon'      => 'users-1',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'All Users',
                        'route'      => 'admin.user.index',
                        'permission' => 'user-list',
                    ],
                    [
                        'label'      => 'Active Users',
                        'route'      => 'admin.user.active',
                        'permission' => 'user-list',
                    ],
                    [
                        'label'      => 'Suspended Users',
                        'route'      => 'admin.user.suspended',
                        'permission' => 'user-list',
                    ],
                    [
                        'label'      => 'Unverified  Users',
                        'route'      => 'admin.user.unverified',
                        'permission' => 'user-list',
                    ],
                    [
                        'label'      => 'KYC Unverified',
                        'route'      => 'admin.user.kyc-unverified',
                        'permission' => 'user-list',
                    ],

                ],
            ],

            [
                'label'     => 'Merchants',
                'code'      => 'merchant-management',
                'icon'      => 'merchant',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Pending Merchants',
                        'route'      => 'admin.merchant.pending',
                        'permission' => 'merchant-list',
                        'icon'       => 'shop',
                    ],
                    [
                        'label'      => 'All Merchants',
                        'route'      => 'admin.merchant.index',
                        'permission' => 'merchant-list',
                        'icon'       => 'shop-1',
                    ],
                    [
                        'label'      => 'Approved Merchants',
                        'route'      => 'admin.merchant.approved',
                        'permission' => 'merchant-list',
                        'icon'       => 'shop',
                    ],
                    [
                        'label'      => 'Rejected Merchants',
                        'route'      => 'admin.merchant.rejected',
                        'permission' => 'merchant-list',
                        'icon'       => 'shop',
                    ],
                ],
            ],
            [
                'label'     => 'KYC Management',
                'icon'      => 'kyc',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Awaiting KYC',
                        'route'      => 'admin.kyc.pending',
                        'permission' => 'kyc-list',
                    ],
                    [
                        'label'      => 'KYC List',
                        'route'      => 'admin.kyc.index',
                        'permission' => 'kyc-list',
                    ],
                    [
                        'label'      => 'KYC Templates',
                        'route'      => 'admin.kyc.template.index',
                        'permission' => 'kyc-template-list',
                    ],
                ],
            ],
        ],
    ],
    [
        'label' => 'Communication',
        'menus' => [
            [
                'label'      => 'Notify To Users',
                'icon'       => 'send-mail',
                'type'       => 'single',
                'route'      => 'admin.notifications.notifyToUser',
                'permission' => 'custom-notify-users',
            ],
            [
                'label'     => 'Notification',
                'icon'      => 'notification',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'All Notifications',
                        'icon'       => 'notification',
                        'route'      => 'admin.notifications.index',
                        'permission' => 'notification-list',
                    ],
                    [
                        'label'      => 'Notifications Template',
                        'icon'       => 'template',
                        'route'      => 'admin.notifications.template.index',
                        'permission' => 'notification-template-list',
                    ],
                ],
            ],
        ],
    ],

    [
        'label' => 'Finance & Wallet',
        'menus' => [
            [
                'label'      => 'Currency Manage',
                'icon'       => 'money-cog',
                'type'       => 'single',
                'route'      => 'admin.currency.index',
                'permission' => 'currency-manage',
            ],
            [
                'label'      => 'Payment Gateways',
                'icon'       => 'payment',
                'type'       => 'single',
                'route'      => 'admin.payment.gateway.index',
                'permission' => 'payment-gateway-list',
            ],
            [
                'label'      => 'Virtual Cards',
                'code'       => 'virtual-card-management',
                'icon'       => 'virtual-card',
                'type'       => 'groups',
                'permission' => 'virtual-card-list',
                'sub_menus'  => [
                    [
                        'label'      => 'Awaiting Requests',
                        'icon'       => 'request-1',
                        'route'      => 'admin.virtual-card.requests.awaiting',
                        'permission' => 'virtual-card-action',
                    ],
                    [
                        'label'      => 'CardHolders',
                        'icon'       => 'cardHolder',
                        'route'      => 'admin.virtual-card.cardholders.index',
                        'permission' => 'virtual-card-action',
                    ],
                    [
                        'label'      => 'All Requests',
                        'icon'       => 'request-list',
                        'route'      => 'admin.virtual-card.requests.all',
                        'permission' => 'virtual-card-list',
                    ],
                    [
                        'label'      => 'Virtual Card List',
                        'icon'       => 'card',
                        'route'      => 'admin.virtual-card.list',
                        'permission' => 'virtual-card-list',
                    ],
                    [
                        'label'      => 'Fee Settings',
                        'icon'       => 'fee',
                        'route'      => 'admin.virtual-card.fee-settings.index',
                        'permission' => 'virtual-card-action',
                    ],
                    [
                        'label'      => 'Provider Configuration',
                        'icon'       => 'provider',
                        'route'      => 'admin.virtual-card.provider.index',
                        'permission' => 'virtual-card-provider-manage',
                    ],
                ],
            ],
            [
                'label'     => 'Deposit',
                'code'      => 'deposit-management',
                'icon'      => 'wallet-plus',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Manual Requests',
                        'icon'       => 'deposit',
                        'route'      => 'admin.deposit.manual-request',
                        'permission' => 'deposit-list',
                    ],
                    [
                        'label'      => 'Automatic Methods',
                        'icon'       => 'auto-payment',
                        'route'      => 'admin.deposit.method.index',
                        'params'     => ['type' => 'auto'],
                        'permission' => 'deposit-method-list',
                    ],
                    [
                        'label'      => 'Manual Methods',
                        'icon'       => 'manual-payment',
                        'route'      => 'admin.deposit.method.index',
                        'params'     => ['type' => 'manual'],
                        'permission' => 'deposit-method-list',
                    ],
                    [
                        'label'      => 'Deposit History',
                        'icon'       => 'deposit-1',
                        'route'      => 'admin.deposit.history',
                        'permission' => 'deposit-list',
                    ],
                ],
            ],
            [
                'label'     => 'Withdraw',
                'code'      => 'withdraw-management',
                'icon'      => 'withdraw-1',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Manual Requests',
                        'icon'       => 'withdraw-1',
                        'route'      => 'admin.withdraw.manual-request',
                        'permission' => 'withdraw-list',
                    ],
                    [
                        'label'      => 'Automatic Methods',
                        'icon'       => 'auto-payment',
                        'route'      => 'admin.withdraw.method.index',
                        'params'     => ['type' => 'auto'],
                        'permission' => 'withdraw-method-list',
                    ],

                    [
                        'label'      => 'Manual Methods',
                        'icon'       => 'manual-payment',
                        'route'      => 'admin.withdraw.method.index',
                        'params'     => ['type' => 'manual'],
                        'permission' => 'withdraw-method-list',
                    ],
                    [
                        'label'      => 'Scheduled Withdraws',
                        'icon'       => 'schedule',
                        'route'      => 'admin.withdraw.schedule',
                        'permission' => 'withdraw-schedule',
                    ],
                    [
                        'label'      => 'Withdraws History',
                        'icon'       => 'withdraw-2',
                        'route'      => 'admin.withdraw.history',
                        'permission' => 'withdraw-list',
                    ],
                ],
            ],
            [
                'label'      => 'Referrals',
                'icon'       => 'referral',
                'type'       => 'single',
                'route'      => 'admin.referral.index',
                'permission' => 'referral-manage',
            ],
            [
                'label'      => 'User Ranking',
                'icon'       => 'ranking',
                'type'       => 'single',
                'route'      => 'admin.ranking.index',
                'permission' => 'ranking-manage',
            ],
            [
                'label'      => 'Transactions',
                'icon'       => 'transaction-2',
                'type'       => 'single',
                'route'      => 'admin.transaction',
                'permission' => 'transaction-list',
            ],
        ],
    ],
    [
        'label' => 'Support Center',
        'menus' => [
            [
                'label'     => 'Support Ticket',
                'icon'      => 'message',
                'code'      => 'support-ticket',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Pending Ticket',
                        'icon'       => 'chat-1',
                        'route'      => 'admin.support-ticket.new',
                        'permission' => 'support-ticket-list',
                    ],
                    [
                        'label'      => 'In Progress Ticket',
                        'icon'       => 'chat-2',
                        'route'      => 'admin.support-ticket.inprogress',
                        'permission' => 'support-ticket-list',
                    ],
                    [
                        'label'      => 'Close Ticket',
                        'icon'       => 'chat-3',
                        'route'      => 'admin.support-ticket.close',
                        'permission' => 'support-ticket-list',
                    ],
                    [
                        'label'      => 'All Ticket',
                        'icon'       => 'chat-4',
                        'route'      => 'admin.support-ticket.history',
                        'permission' => 'support-ticket-list',
                    ],
                ],
            ],
            [
                'label'      => 'Support Category',
                'icon'       => 'tags',
                'type'       => 'single',
                'route'      => 'admin.support-ticket.category.index',
                'permission' => 'support-ticket-category-manage',
            ],
        ],
    ],
    [
        'label' => 'System Config',
        'menus' => [
            [
                'label'     => 'Settings',
                'code'      => 'settings-management',
                'icon'      => 'cil-settings',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Site Settings',
                        'icon'       => 'site-setting',
                        'route'      => 'admin.settings.site.index',
                        'permission' => 'site-setting-view',
                    ],
                    [
                        'label'      => 'Plugins Manage',
                        'icon'       => 'cil-fork',
                        'route'      => 'admin.settings.plugin.index',
                        'permission' => 'plugins-manage',
                    ],
                    [
                        'label'      => 'Notifications',
                        'icon'       => 'notification',
                        'route'      => 'admin.settings.plugin_type',
                        'permission' => 'plugins-manage',
                        'params'     => ['plugin_type' => 'notification'],
                    ],
                    [
                        'label'      => 'Exchange Rates',
                        'icon'       => 'currency-exchange',
                        'route'      => 'admin.settings.plugin_type',
                        'permission' => 'plugins-manage',
                        'params'     => ['plugin_type' => 'exchange_rate'],
                    ],
                ],
            ],
            [
                'label'      => 'Language',
                'icon'       => 'translate',
                'type'       => 'single',
                'route'      => 'admin.language.index',
                'permission' => 'language-list',
            ],
        ],
    ],
    [
        'label' => 'Staff Management',
        'menus' => [
            [
                'label'      => 'Staff',
                'icon'       => 'badge-account',
                'type'       => 'single',
                'route'      => 'admin.staff.index',
                'permission' => 'staff-list',
            ],
            [
                'label'      => 'Roles & Permissions',
                'icon'       => 'role',
                'type'       => 'single',
                'route'      => 'admin.role.index',
                'permission' => 'role-list',
            ],
        ],
    ],
    [
        'label' => 'CMS Essentials',
        'menus' => [
            [
                'label'      => 'Custom Landing Page',
                'icon'       => 'custom-landing',
                'type'       => 'single',
                'route'      => 'admin.custom-landing.index',
                'permission' => 'page-list',
            ],
            [
                'label'      => 'Navigation Manage',
                'icon'       => 'list-2',
                'type'       => 'single',
                'route'      => 'admin.navigation.site.index',
                'permission' => 'navigation-manage',
            ],
            [
                'label'     => 'Pages',
                'icon'      => 'page',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Page Manage',
                        'route'      => 'admin.page.site.index',
                        'permission' => 'page-list',
                    ],
                    [
                        'label'      => 'Component Manage',
                        'route'      => 'admin.page.component.index',
                        'permission' => 'component-list',
                    ],
                ],
            ],
            [
                'label'      => 'Footer Manage',
                'icon'       => 'footer',
                'type'       => 'single',
                'route'      => 'admin.page.footer.section.index',
                'permission' => 'page-footer-manage',
            ],
            [
                'label'     => 'Blog',
                'icon'      => 'blog',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Blog',
                        'route'      => 'admin.blog.post.index',
                        'permission' => 'blog-list',
                    ],
                    [
                        'label'      => 'Category',
                        'route'      => 'admin.blog.category.index',
                        'permission' => 'blog-category-list',
                    ],
                ],
            ],
            [
                'label'      => 'Subscribers',
                'icon'       => 'email',
                'type'       => 'single',
                'route'      => 'admin.subscriber.index',
                'permission' => 'subscriber-list',
            ],

            [
                'label'      => 'Social Links',
                'icon'       => 'social-link',
                'type'       => 'single',
                'route'      => 'admin.social.index',
                'permission' => 'social-list',
            ],
            [
                'label'      => 'SEO Manage',
                'icon'       => 'seo',
                'type'       => 'single',
                'route'      => 'admin.site-seo.index',
                'permission' => 'seo-manage',
            ],
        ],
    ],
    [
        'label' => 'Monitoring & Logs',
        'menus' => [
            [
                'label'      => 'Activity Log',
                'icon'       => 'user-activity',
                'type'       => 'single',
                'route'      => 'admin.activity-log',
                'permission' => 'user-activity-log',
            ],
        ],
    ],
    [
        'label' => 'Application',
        'menus' => [
            [
                'label'      => 'Style Manager',
                'icon'       => 'app-style-code',
                'type'       => 'single',
                'route'      => 'admin.app.style-manager',
                'permission' => 'style-manager',
            ],
            [
                'label'     => 'Optimize App',
                'icon'      => 'optimize',
                'type'      => 'groups',
                'sub_menus' => [
                    [
                        'label'      => 'Optimize App',
                        'route'      => 'admin.app.optimize',
                        'permission' => 'app-optimize',
                    ],
                    [
                        'label'      => 'Clear Cache App',
                        'route'      => 'admin.app.clear-cache',
                        'permission' => 'app-clear-cache',
                    ],
                ],
            ],
            [
                'label'      => 'App Info',
                'icon'       => 'app',
                'type'       => 'single',
                'route'      => 'admin.app.info',
                'permission' => 'app-info',
            ],
        ],
    ],

];
