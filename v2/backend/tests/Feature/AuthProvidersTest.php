<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthProvidersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exposes_available_authentication_providers(): void
    {
        config()->set('sso.microsoft.enabled', true);
        config()->set('services.azure.client_id', 'client-id');
        config()->set('services.azure.client_secret', 'client-secret');

        $this->getJson('/api/auth/providers')
            ->assertOk()
            ->assertJsonPath('providers.0.key', 'local')
            ->assertJsonPath('providers.1.key', 'microsoft')
            ->assertJsonPath('providers.1.enabled', true)
            ->assertJsonPath('providers.1.login_url', '/auth/microsoft/redirect');
    }
}