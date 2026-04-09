<?php

namespace Tests\Feature;

use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_and_fetch_profile(): void
    {
        $this->seed(AccessControlSeeder::class);

        $user = \App\Models\User::query()->create([
            'name' => 'Admin User',
            'display_name' => 'Admin User',
            'login_name' => 'admin',
            'email' => 'admin@example.local',
            'password' => 'secret123',
            'status' => 'active',
        ]);

        $roleId = \App\Models\Role::query()->where('slug', 'superadmin')->value('id');
        $user->roles()->sync([$roleId]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'admin@example.local',
            'password' => 'secret123',
            'device_name' => 'PHPUnit',
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'user' => ['id', 'email', 'roles', 'permissions'],
            ]);

        $token = $loginResponse->json('access_token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'admin@example.local')
            ->assertJsonPath('user.roles.0.slug', 'superadmin');
    }
}