<?php

return [
    'enabled' => env('PAYSTACK_ENABLED', true),
    'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co/'),
    'secret_key' => env('PAYSTACK_SECRET_KEY', null),
    'public_key' => env('PAYSTACK_PUBLIC_KEY', null),
    'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET', null),
    'currency' => env('PAYSTACK_CURRENCY', 'NGN'),
    'default_bank_code' => env('PAYSTACK_VIRTUAL_WALLET_DEFAULT_BANK_CODE', '035'),
    'default_bank_name' => env('PAYSTACK_VIRTUAL_WALLET_DEFAULT_BANK_NAME', 'Wema Bank'),
]; 
