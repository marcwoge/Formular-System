<?php

return [
    'default_logo_url' => env('BRANDING_DEFAULT_LOGO_URL', '/brand-assets/formshub.png'),
    'custom_logo_url' => env('BRANDING_CUSTOM_LOGO_URL'),
    'custom_app_name' => env('BRANDING_CUSTOM_APP_NAME'),
    'show_vendor_footer_logo' => filter_var(env('BRANDING_SHOW_VENDOR_FOOTER_LOGO', true), FILTER_VALIDATE_BOOL),
    'force_vendor_when_unlicensed' => filter_var(env('BRANDING_FORCE_VENDOR_WHEN_UNLICENSED', true), FILTER_VALIDATE_BOOL),
];