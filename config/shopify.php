<?php

return [
    'app_name' => env('SHOPIFY_APP_NAME', 'Todo_App'),
    'shopify_api_key' => env('SHOPIFY_API_KEY', 'df46ee1db7f21fb23fc58a2a48086484'),
    'shopify_api_secret' => env('SHOPIFY_API_SECRET', 'a5454df6f569ae1b8047423d7233ab94'),
    'app_embedded' => env('APP_EMBEDDED', false),
    'turbo_enabled' => (bool) env('SHOPIFY_TURBO_ENABLED', false),
    'shopify_api_version' => '2024-10',
    //
    'fulfillment_service_name' => env('APP_NAME', 'Test app'),
    'session_token_refresh_interval' => env('SESSION_TOKEN_REFRESH_INTERVAL', 2000),
    'myshopify_domain' => env('SHOPIFY_MYSHOPIFY_DOMAIN', 'myshopify.com'),
    'shop_auth_provider' => env('SHOPIFY_SHOP_AUTH_PROVIDER', 'users'),
    //
    'add_to_cart_tag_product' => 'buy-now',
    'api_scopes' => [
        'write_orders',
        'write_fulfillments',
        'write_customers',
        'write_products',
        'read_third_party_fulfillment_orders',
        'write_third_party_fulfillment_orders',
        'write_assigned_fulfillment_orders',
        'read_assigned_fulfillment_orders',
        'read_merchant_managed_fulfillment_orders',
        'write_merchant_managed_fulfillment_orders'
    ],
    //
    'webhook_events' => [
        'products/create' => 'product/created', 
        'app/uninstalled' => 'app/uninstall',
        'shop/update' => 'shop/updated',
    ],
    //
    'default_permissions' => [
        'write-products',
        'read-products',
        'write-orders',
        'read-orders',
        'write-customers',
        'read-customers',
        'write-members',
        'read-members'
    ],
    //
    'one_time_payments' => [
        1 => [
            'name' => 'Simple',
            'price' => 4.99,
            'credits' => 500
        ],
        2 => [
            'name' => 'Extra',
            'price' => 9.99,
            'credits' => 2500
        ],
        3 => [
            'name' => 'Large',
            'price' => 14.99,
            'credits' => 5000
        ]
    ],
    //
    'subscription_is_test' => true


];
