<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Application default settings
    |--------------------------------------------------------------------------
    |
    | Settings that are stored in the database by key value pairs with a flag
    | for public. Public values are used for layout in the frontend.
    | they are generated on customer creation in the EventServiceProvider
    |
    */

    'default' => [
        [
            'public' => '0',
            'key' => 'login_endpoint',
            'value' => '',
        ],        
        [
            'public' => '1',
            'key' => 'app_name',
            'value' => 'Tenants',
        ],
        [
            'public' => '0',
            'key' => 'enable_registration',
            'value' => '0',
        ],
        [
            'public' => '1',
            'key' => 'primary_color',
            'value' => '#002e60',
        ],
        [
            'public' => '1',
            'key' => 'secondary_color',
            'value' => '#333',
        ],
        [
            'public' => '0',
            'key' => 'mollie_api_key',
            'value' => 'mollie api key',
        ],
        [
            'public' => '0',
            'key' => 'mollie_webhook_url',
            'value' => 'mollie webhook',
        ],
        [
            'public' => '0',
            'key' => 'show_locations_booking',
            'value' => '0',
        ]        
    ]
];
