<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_free_edition_status_without_remote_license_configuration(): void
    {
        config()->set('licensing.server_url', null);
        config()->set('licensing.activation_key', null);
        config()->set('licensing.product_name', 'FormsHub');
        config()->set('licensing.product_code', 'formshub');
        config()->set('licensing.free_edition_notice.enabled', true);
        config()->set('licensing.free_edition_notice.text', 'FormsHub Free Edition - this installation is currently running without a valid commercial license.');
        config()->set('branding.default_logo_url', '/brand-assets/formshub.png');
        config()->set('branding.custom_logo_url', '/custom-assets/customer-logo.png');
        config()->set('branding.custom_app_name', 'Customer Portal');
        config()->set('branding.force_vendor_when_unlicensed', true);

        $response = $this->getJson('/api/system/license-status');

        $response->assertOk()
            ->assertJsonPath('product.name', 'FormsHub')
            ->assertJsonPath('product.code', 'formshub')
            ->assertJsonPath('license.edition', 'free')
            ->assertJsonPath('license.is_valid', false)
            ->assertJsonPath('license.reason', 'missing_remote_configuration')
            ->assertJsonPath('free_edition_notice.enabled', true)
            ->assertJsonPath('branding.display_name', 'FormsHub')
            ->assertJsonPath('branding.primary_logo_url', '/brand-assets/formshub.png')
            ->assertJsonPath('branding.footer_logo_url', '/brand-assets/formshub.png')
            ->assertJsonPath('branding.force_vendor_branding', true);

        $this->assertDatabaseHas('license_connectors', [
            'name' => 'default',
            'product_code' => 'formshub',
            'is_valid' => false,
        ]);
    }
}