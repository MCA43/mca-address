<?php

return [

    'enabled' => env('MCA_ADDRESS_ENABLED', true),

    'locale' => env('MCA_ADDRESS_LOCALE'),

    'tables' => [
        'countries' => env('MCA_ADDRESS_COUNTRIES_TABLE', 'mca_countries'),
        'cities' => env('MCA_ADDRESS_CITIES_TABLE', 'mca_cities'),
        'districts' => env('MCA_ADDRESS_DISTRICTS_TABLE', 'mca_districts'),
        'neighborhoods' => env('MCA_ADDRESS_NEIGHBORHOODS_TABLE', 'mca_neighborhoods'),
    ],

    'access' => [
        'use_permission_root' => env('MCA_ADDRESS_USE_PERMISSION_ROOT', true),
        'role_column' => env('MCA_ADDRESS_ROLE_COLUMN', 'role_id'),
        'root_role' => env('MCA_ADDRESS_ROOT_ROLE', 'root'),
    ],

    'api' => [
        'default_country_id' => env('MCA_ADDRESS_DEFAULT_COUNTRY_ID'),
        'middleware' => ['web'],
    ],

    'uavt' => [
        'enabled' => env('MCA_ADDRESS_UAVT_ENABLED', false),
        'driver' => env('MCA_ADDRESS_UAVT_DRIVER', 'none'),
        'api_url' => env('MCA_ADDRESS_UAVT_API_URL'),
        'api_key' => env('MCA_ADDRESS_UAVT_API_KEY'),
        'fallback_local' => env('MCA_ADDRESS_UAVT_FALLBACK_LOCAL', true),
    ],

    'routes' => [
        'load_package_routes' => env('MCA_ADDRESS_LOAD_ROUTES', true),
        'name_prefix' => 'mca.address.',
        'web' => [
            'enabled' => env('MCA_ADDRESS_WEB_ENABLED', true),
            'prefix' => env('MCA_ADDRESS_ROUTE_PREFIX', 'mca/address'),
            'middleware' => ['web', 'auth', 'mca.address.root', 'mca.address.locale'],
        ],
        'api' => [
            'enabled' => env('MCA_ADDRESS_API_ENABLED', true),
            'prefix' => env('MCA_ADDRESS_API_PREFIX', 'mca/address/api'),
            'middleware' => ['web'],
        ],
    ],

    'import' => [
        'turkey' => [
            'neighbourhoods_file' => env('MCA_ADDRESS_NEIGHBOURHOODS_FILE'),
        ],
    ],

    'ui' => [
        'title' => env('MCA_ADDRESS_UI_TITLE'),
        'per_page' => (int) env('MCA_ADDRESS_PER_PAGE', 20),
        'assets' => [
            'ui' => 'vendor/mca-permission/mca-ui.css',
            'ui_js' => 'vendor/mca-permission/mca-ui.js',
            'perm' => 'vendor/mca-permission/mca-permission.css',
            'css' => 'vendor/mca-address/mca-address.css',
            'js' => 'vendor/mca-address/mca-address.js',
        ],
    ],

    'views' => [
        'namespace' => env('MCA_ADDRESS_VIEW_NAMESPACE', 'mca-address'),
        'layout' => env('MCA_ADDRESS_VIEW_LAYOUT', 'mca-address::layouts.app'),
    ],

];
