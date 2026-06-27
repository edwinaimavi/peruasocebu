<?php

return [

    'title' => 'PERU ASOCEBU',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => false,

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |------------------------------------------------------------------
    | Branding
    |------------------------------------------------------------------
    */
    'logo' => '<span>PERU ASOCEBU</span>',
    'logo_img' => 'vendor/adminlte/dist/img/logo2.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_alt' => 'PERU ASOCEBU',

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo2.png',
            'alt' => 'PERU ASOCEBU',
            'class' => 'img-circle elevation-3',
            'width' => 70,
            'height' => 70,
        ],
    ],

    /*
    |------------------------------------------------------------------
    | Preloader
    |------------------------------------------------------------------
    */
    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo2.png',
            'alt' => 'Cargando PERU ASOCEBU...',
            'effect' => 'animation__pulse',
            'width' => 80,
            'height' => 80,
        ],
    ],

    /*
    |------------------------------------------------------------------
    | User Menu
    |------------------------------------------------------------------
    */
    'usermenu_enabled' => false,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-success',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |------------------------------------------------------------------
    | Layout
    |------------------------------------------------------------------
    */
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_light_mode' => null,

    /*
    |------------------------------------------------------------------
    | Auth Views
    |------------------------------------------------------------------
    */
    'classes_auth_card' => 'card-outline card-info shadow',
    'classes_auth_btn' => 'btn-flat btn-info',

    /*
    |------------------------------------------------------------------
    | Admin Panel Classes
    |------------------------------------------------------------------
    */
    'classes_body' => 'text-sm',
    'classes_sidebar' => 'sidebar-dark-success elevation-4',
    'classes_topnav' => 'navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container-fluid',

    /*
    |------------------------------------------------------------------
    | Sidebar
    |------------------------------------------------------------------
    */
    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_scrollbar_theme' => 'os-theme-dark',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 200,

    /*
    |------------------------------------------------------------------
    | URLs
    |------------------------------------------------------------------
    */
    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'profile_url' => 'admin/profile',

    /*
    |------------------------------------------------------------------
    | Menu
    |------------------------------------------------------------------
    */
    'menu' => [

        ['type' => 'fullscreen-widget', 'topnav_right' => true],

        ['header' => 'ADMINISTRACIÓN'],
        ['text' => 'Dashboard', 'url' => 'home', 'icon' => 'fas fa-chart-line'],

        [
            'text' => 'Usuarios',
            'icon' => 'fas fa-users-cog',
            'submenu' => [
                ['text' => 'Roles', 'url' => 'admin/roles', 'icon' => 'fas fa-user-shield'],
                ['text' => 'Usuarios', 'url' => 'admin/users', 'icon' => 'fas fa-users'],
            ],
        ],

        ['header' => 'GANADERIA'],
        ['text' => 'Criaderos / Haciendas', 'url' => 'admin/ranches', 'icon' => 'fas fa-warehouse', 'can' => 'admin.ranches.index'],
        ['text' => 'Propietarios', 'url' => 'admin/owners', 'icon' => 'fas fa-users', 'can' => 'admin.owners.index'],
        ['text' => 'Veterinarios', 'url' => 'admin/veterinarians', 'icon' => 'fas fa-user-md', 'can' => 'admin.veterinarians.index'],
        ['text' => 'Razas', 'url' => 'admin/breeds', 'icon' => 'fas fa-dna', 'can' => 'admin.breeds.index'],
        ['text' => 'Ganado', 'url' => 'admin/cattle', 'icon' => 'fas fa-paw', 'can' => 'admin.cattle.index'],
        ['text' => 'Genealogía', 'url' => 'admin/cattle-genealogy', 'icon' => 'fas fa-sitemap', 'can' => 'admin.cattle-genealogy.index'],
        

        ['header' => 'CUENTA'],
        ['text' => 'Perfil', 'url' => 'admin/settings', 'icon' => 'fas fa-user'],
        ['text' => 'Cerrar sesión', 'url' => 'logout', 'icon' => 'fas fa-sign-out-alt'],
    ],

    /*
    |------------------------------------------------------------------
    | Menu Filters
    |------------------------------------------------------------------
    */
    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |------------------------------------------------------------------
    | Plugins
    |------------------------------------------------------------------
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
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
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
    ],

    /*
    |------------------------------------------------------------------
    | Custom CSS
    |------------------------------------------------------------------
    */
    'custom_css' => [
        'css/admin-modern.css',
    ],

    'livewire' => false,
];
