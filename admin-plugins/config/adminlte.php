<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'BillConector',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Bill</b>CONECTOR',
    'logo_img' => false,
    'logo_img_class' => false,
    'logo_img_xl' => false,
    'logo_img_xl_class' => false,
    'logo_img_alt' => false,

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => false,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        [
            'text'  => 'Dashboard',
            'url'   => 'admin/dashboard',
            'can'   => 'is-admin',
            'icon'  => 'fas fa-tachometer-alt'
        ],
        [
            'text'      => 'plans',
            'url'       => 'admin/plans',
            'icon'      => 'fas fa-handshake',
            'can'       => 'is-admin',
            'active'    => ['admin/plans*'],
        ],
        [
            'text'      => 'countries',
            'url'       => 'admin/countries',
            'icon'      => 'fas fa-globe-americas',
            'can'       => 'is-admin',
            'active'    => ['admin/countries*'],
        ],
        [
            'header'    => 'AGENCIES',
            'can'       => 'is-admin',
        ],
        [
            'text'      => 'agencies',
            'url'       => 'admin/agencies',
            'icon'      => 'fas fa-building',
            'can'       => 'is-admin',
            'active'    => ['admin/agencies*'],
        ],
        [
            'text'      => 'bank_account',
            'url'       => 'admin/bank-account',
            'icon'      => 'fas fa-university',
            'can'       => 'is-admin',
            'active'    => ['admin/bank-account*'],
        ],
        [
            'text'      => 'transfer_request',
            'url'       => 'admin/transfer-request',
            'icon'      => 'fas fa-exchange-alt',
            'can'       => 'is-admin',
            'active'    => ['admin/transfer-request*'],
        ],
        [
            'header'    => 'CUSTOMERS',
            'can'       => 'is-admin',
        ],
        [
            'text'      => 'customers',
            'url'       => 'admin/customers',
            'icon'      => 'fas fa-fw fa-user',
            'can'       => 'is-admin',
            'active'    => ['admin/customers*'],
        ],
        [
            'text'      => 'payments',
            'url'       => 'admin/payments',
            'icon'      => 'fas fa-money-check-alt',
            'can'       => 'is-admin',
            'active'    => ['admin/payments*'],
        ],
        [
            'text'      => 'access_token',
            'url'       => 'admin/access-token',
            'icon'      => 'fas fa-key',
            'can'       => 'is-admin',
            'active'    => ['admin/access-token*'],
        ],
        [
            'text'      => 'user_keys',
            'url'       => 'admin/user-key',
            'icon'      => 'fas fa-cog',
            'can'       => 'is-admin',
            'active'    => ['admin/user-key*'],
        ],
        // Agencia
        [
            'text'      => 'Dashboard',
            'url'       => 'agency/dashboard',
            'can'       => 'is-agency',
            'icon'      => 'fas fa-tachometer-alt',
            'active'    => [
                'agency/withdraw-funds',
                'agency/dashboard'
            ],
        ],
        [
            'text'      => 'customers',
            'url'       => 'agency/customers',
            'icon'      => 'fas fa-fw fa-user',
            'can'       => 'is-agency',
            'active'    => ['agency/customers*'],
        ],
        [
            'text'      => 'plans',
            'url'       => 'agency/plans',
            'can'       => 'is-agency',
            'icon'      => 'fas fa-handshake',
            'active'    => ['agency/plans*'],
        ],
        [
            'text'      => 'bank_account',
            'url'       => 'agency/bank-account',
            'icon'      => 'fas fa-university',
            'can'       => 'is-agency',
            'active'    => ['agency/bank-account*'],
        ],
        //      funcionamiento en "stop"

        // [
        //     'text'      => 'Connect Account',
        //     'url'       => 'agency/stripe-connect',
        //     'can'       => 'is-agency',
        //     'icon'      => 'fab fa-stripe-s',
        //     'active'    => ['agency/stripe-connect*'],
        // ],

        // Customer
        /**
         * * Menú base para los clientes
         * * sin importar su agencia
         * @author Matías
         */
        [
            'text'      => 'Home',
            'url'       => 'home',
            'icon'      => 'fas fa-home',
            'can'       => ['is-customer'],
            'active'    => ['home*'],
        ],
        
        [
            'text'      => 'subscriptions',
            'url'       => 'subscriptions',
            'icon'      => 'fas fa-handshake',
            'can'       => ['is-customer'],
            'active'    => ['subscriptions*'],
        ],        
        [
            'text'      => 'billing',
            'url'       => 'billing',
            'icon'      => 'fas fa-file-invoice',
            'can'       => ['is-customer'],
            'active'    => ['billing*'],
        ],

        /**
         * * Opción de tarjeta SOLO para BeOn24
         * @author Matías
         */
        [
            'text'      => 'cards',
            'url'       => 'cards',
            'icon'      => 'fa fa-credit-card',
            'can'       => ['is-customer-beon24'],
            'active'    => ['credit-card*'],
        ],
        
        [
            'header'    => 'account_settings',
            'can'       => ['is-customer'],
        ],

        
        
        /**
         * Menu para los clientes finales que no son de LARS CHILE
         */
        [
            'text'      => 'card_binding',
            'url'       => 'credit-card',
            'icon'      => 'fa fa-credit-card',
            'can'       => ['is-customer-colombia'],
            'active'    => ['credit-card*'],
        ],
        [
            'text'      => 'Orders',
            'url'       => 'orders',
            'icon'      => 'fas fa-file-archive',
            'can'       => ['is-customer-colombia'],
            'active'    => ['orders*'],
        ],
        [
            'text'      => 'Plugin',
            'url'       => 'plugin',
            'icon'      => 'fas fa-file-archive',
            'can'       => ['is-customer-colombia'],
            'active'    => ['plugin*'],
        ],
        [
            'text'      => 'access_token',
            'url'       => 'access-token',
            'icon'      => 'fas fa-key',
            'can'       => ['is-customer-colombia'],
            'active'    => ['access-token*'],
        ],
        [
            'text'      => 'configure_keys',
            'url'       => 'configure-keys',
            'icon'      => 'fas fa-cog',
            'can'       => 'is-customer-colombia',
            'active'    => ['configure-keys*'],
        ],
        [
            'text'      => 'bank_account',
            'url'       => 'bank-account',
            'icon'      => 'fas fa-university',
            'can'       => 'is-customer-colombia',
            'active'    => ['bank-account*'],
        ],
        [
            'text'      => 'companies',
            'url'       => 'col-companies',
            'icon'      => 'fas fa-university',
            'can'       => 'is-customer-colombia',
            'active'    => ['companies*'],
        ],
        /**
         * Menu para los clientes finales de LARS CHILE
         */
        [
            'text'      => 'card_binding',
            'url'       => 'credit-card',
            'icon'      => 'fa fa-credit-card',
            'can'       => ['is-customer-chile'],
            'active'    => ['credit-card*'],
        ],
        [
            'text'      => 'Orders',
            'url'       => 'orders',
            'icon'      => 'fas fa-file-archive',
            'can'       => ['is-customer-chile'],
            'active'    => ['orders*'],
        ],
        [
            'text'      => 'Plugin',
            'url'       => 'plugin',
            'icon'      => 'fas fa-file-archive',
            'can'       => ['is-customer-chile'],
            'active'    => ['plugin*'],
        ],
        [
            'text'      => 'access_token',
            'url'       => 'access-token',
            'icon'      => 'fas fa-key',
            'can'       => ['is-customer-chile'],
            'active'    => ['access-token*'],
        ],
        [
            'text'      => 'companies',
            'url'       => 'companies',
            'icon'      => 'fas fa-university',
            'can'       => 'is-customer-chile',
            'active'    => ['companies*'],
        ],

        /**
         * Menú para los clientes finales de LARS BEON 24
         * @author Matías
         */
        
        [
            'text'      => 'companies',
            'url'       => 'col-companies',
            'icon'      => 'fas fa-university',
            'can'       => 'is-customer-beon24',
            'active'    => ['companies*'],
        ],
        
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],

                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'jConfirm' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => "/css/jConfirm.css",
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => "/js/jConfirm.js",
                ],
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    */

    'livewire' => false,
];
