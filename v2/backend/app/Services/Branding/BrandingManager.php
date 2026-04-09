<?php

namespace App\Services\Branding;

class BrandingManager
{
    public function resolve(array $licenseStatus): array
    {
        $defaultLogoUrl = (string) config('branding.default_logo_url', '/brand-assets/formshub.png');
        $customLogoUrl = trim((string) config('branding.custom_logo_url', ''));
        $customAppName = trim((string) config('branding.custom_app_name', ''));
        $vendorName = (string) config('licensing.product_name', 'FormsHub');
        $licensed = (bool) data_get($licenseStatus, 'license.is_valid', false);
        $whiteLabelConfigured = $customLogoUrl !== '' || $customAppName !== '';
        $forceVendorBranding = ! $licensed && (bool) config('branding.force_vendor_when_unlicensed', true);

        $effectiveLogoUrl = $defaultLogoUrl;
        $effectiveAppName = $vendorName;
        $mode = 'default';

        if ($whiteLabelConfigured && ! $forceVendorBranding) {
            $effectiveLogoUrl = $customLogoUrl !== '' ? $customLogoUrl : $defaultLogoUrl;
            $effectiveAppName = $customAppName !== '' ? $customAppName : $vendorName;
            $mode = 'white_label';
        }

        if ($forceVendorBranding) {
            $mode = 'vendor_enforced';
        }

        $showVendorFooterLogo = (bool) config('branding.show_vendor_footer_logo', true)
            && ($forceVendorBranding || $whiteLabelConfigured);

        return [
            'mode' => $mode,
            'display_name' => $effectiveAppName,
            'licensed' => $licensed,
            'white_label_configured' => $whiteLabelConfigured,
            'force_vendor_branding' => $forceVendorBranding,
            'primary_logo_url' => $effectiveLogoUrl,
            'vendor_logo_url' => $defaultLogoUrl,
            'footer_logo_url' => $showVendorFooterLogo ? $defaultLogoUrl : null,
        ];
    }
}