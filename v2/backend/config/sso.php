<?php

return [
    'microsoft' => [
        'enabled' => filter_var(env('MS365_SSO_ENABLED', false), FILTER_VALIDATE_BOOL),
        'auto_provision' => filter_var(env('MS365_SSO_AUTO_PROVISION', true), FILTER_VALIDATE_BOOL),
        'allow_guest_users' => filter_var(env('MS365_SSO_ALLOW_GUEST_USERS', false), FILTER_VALIDATE_BOOL),
        'allowed_email_domains' => array_values(array_filter(array_map(
            static fn (string $domain) => trim(strtolower($domain)),
            explode(',', (string) env('MS365_SSO_ALLOWED_EMAIL_DOMAINS', ''))
        ))),
        'frontend_redirect_url' => env('SSO_FRONTEND_REDIRECT_URL', env('APP_URL', 'http://localhost:8080').'/'),
        'default_role_slug' => env('MS365_SSO_DEFAULT_ROLE', 'standardbenutzer'),
        'provider_name' => env('MS365_SSO_PROVIDER_NAME', 'microsoft-365'),
    ],
];