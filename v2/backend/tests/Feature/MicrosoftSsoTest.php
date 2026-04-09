<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class MicrosoftSsoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_callback_creates_user_external_identity_and_redirects_with_token(): void
    {
        $this->seed(AccessControlSeeder::class);

        config()->set('sso.microsoft.enabled', true);
        config()->set('services.azure.client_id', 'client-id');
        config()->set('services.azure.client_secret', 'client-secret');
        config()->set('sso.microsoft.frontend_redirect_url', 'http://localhost:8080');
        config()->set('sso.microsoft.default_role_slug', 'standardbenutzer');

        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn('entra-user-1');
        $socialiteUser->shouldReceive('getEmail')->andReturn('user@example.local');
        $socialiteUser->shouldReceive('getName')->andReturn('Test User');
        $socialiteUser->user = [
            'id' => 'entra-user-1',
            'userPrincipalName' => 'user@example.local',
            'mail' => 'user@example.local',
            'displayName' => 'Test User',
            'department' => 'IT',
            'officeLocation' => 'Ahrensburg',
        ];

        $provider = Mockery::mock();
        $provider->shouldReceive('scopes')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('azure')->andReturn($provider);

        $response = $this->get('/auth/microsoft/callback');

        $response->assertRedirect();
        $this->assertStringContainsString('#sso=success', $response->headers->get('Location'));
        $this->assertStringContainsString('access_token=', $response->headers->get('Location'));

        $user = \App\Models\User::query()->where('email', 'user@example.local')->firstOrFail();
        $this->assertSame('Test User', $user->display_name);
        $this->assertDatabaseHas('external_identities', [
            'user_id' => $user->id,
            'external_identifier' => 'entra-user-1',
        ]);

        $roleId = Role::query()->where('slug', 'standardbenutzer')->value('id');
        $this->assertTrue($user->roles()->where('roles.id', $roleId)->exists());
    }
}