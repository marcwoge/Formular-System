<?php

return [
    'product_name' => env('PRODUCT_NAME', 'FormsHub'),
    'product_code' => env('LICENSE_PRODUCT_CODE', 'formshub'),
    'driver' => env('LICENSE_DRIVER', 'license_server'),
    'server_url' => env('LICENSE_SERVER_URL'),
    'activation_key' => env('LICENSE_ACTIVATION_KEY'),
    'mode' => env('LICENSE_MODE', 'free_fallback'),
    'installation_id' => env('LICENSE_INSTALLATION_ID'),
    'device_name' => env('LICENSE_DEVICE_NAME', 'FormsHub Server'),
    'request_timeout' => (int) env('LICENSE_REQUEST_TIMEOUT', 5),
    'validate_interval_minutes' => (int) env('LICENSE_VALIDATE_INTERVAL_MINUTES', 15),
    'free_edition_notice' => [
        'enabled' => filter_var(env('FREE_EDITION_NOTICE_ENABLED', true), FILTER_VALIDATE_BOOL),
        'text' => env(
            'FREE_EDITION_NOTICE_TEXT',
            'FormsHub Free Edition - this installation is currently running without a valid commercial license.'
        ),
    ],
];