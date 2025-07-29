<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayMongo Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for PayMongo payment gateway
    | integration for the ShoPilipinas VIP Portal.
    |
    */

    'public_key' => env('PAYMONGO_PUBLIC_KEY'),
    'secret_key' => env('PAYMONGO_SECRET_KEY'),
    'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET'),
    'base_url' => env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | Membership Pricing
    |--------------------------------------------------------------------------
    |
    | Define the pricing for each VIP membership tier in centavos (PHP)
    |
    */
    'membership_prices' => [
        'gold' => 300000, // ₱3,000 in centavos
        'platinum' => 3000000, // ₱30,000 in centavos
        'diamond' => 30000000, // ₱300,000 in centavos
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Rates
    |--------------------------------------------------------------------------
    |
    | Define commission rates for different user types and referral tiers
    |
    */
    'commission_rates' => [
        'free' => [
            'gold' => 3.0, // 3%
            'platinum' => 5.0, // 5%
            'diamond' => 0.0, // Not allowed
        ],
        'vip' => [
            'gold' => 5.0, // 5%
            'platinum' => 8.0, // 8%
            'diamond' => 12.0, // 12%
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Supported payment methods for PayMongo
    |
    */
    'payment_methods' => [
        'card',
        'gcash',
        'grab_pay',
        'paymaya',
    ],
];
