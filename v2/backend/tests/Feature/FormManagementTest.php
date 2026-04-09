<?php

namespace Tests\Feature;

use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_version_and_publish_a_form(): void
    {
        $this->seed(AccessControlSeeder::class);

        $user = \App\Models\User::query()->create([
            'name' => 'Forms Admin',
            'display_name' => 'Forms Admin',
            'login_name' => 'forms-admin',
            'email' => 'forms-admin@example.local',
            'password' => 'secret123',
            'status' => 'active',
        ]);

        $roleId = \App\Models\Role::query()->where('slug', 'superadmin')->value('id');
        $user->roles()->sync([$roleId]);

        Sanctum::actingAs($user, ['*']);

        $createResponse = $this->postJson('/api/forms', [
            'slug' => 'it-neuer-mitarbeiter',
            'title' => 'Neuer Mitarbeiter',
            'description' => 'Anlage eines neuen Mitarbeiters',
            'category' => 'IT',
            'visibility_scope' => 'internal',
            'sensitivity' => 'sensitive',
            'allowed_contexts' => ['internal'],
            'allow_user_edits' => true,
            'allow_corrections' => true,
            'definition' => [
                'layout' => 'single-step',
                'fields' => [
                    ['type' => 'text', 'name' => 'firstname', 'label' => 'Vorname', 'required' => true],
                    ['type' => 'text', 'name' => 'lastname', 'label' => 'Nachname', 'required' => true],
                ],
            ],
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.slug', 'it-neuer-mitarbeiter')
            ->assertJsonPath('data.current_version.version_number', 1);

        $formId = $createResponse->json('data.id');

        $versionResponse = $this->postJson("/api/forms/{$formId}/versions", [
            'title' => 'Neuer Mitarbeiter',
            'description' => 'Anlage eines neuen Mitarbeiters inkl. Vorgesetztenfeld',
            'change_summary' => 'Fuegt Vorgesetztenfeld hinzu',
            'definition' => [
                'layout' => 'single-step',
                'fields' => [
                    ['type' => 'text', 'name' => 'firstname', 'label' => 'Vorname', 'required' => true],
                    ['type' => 'text', 'name' => 'lastname', 'label' => 'Nachname', 'required' => true],
                    ['type' => 'text', 'name' => 'manager', 'label' => 'Vorgesetzter', 'required' => false],
                ],
            ],
        ]);

        $versionResponse->assertCreated()
            ->assertJsonPath('data.version_number', 2);

        $versionId = $versionResponse->json('data.id');

        $publishResponse = $this->postJson("/api/forms/{$formId}/publish/{$versionId}");

        $publishResponse->assertOk()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.current_version.status', 'published')
            ->assertJsonPath('data.current_version.version_number', 2);

        $this->getJson('/api/forms')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'it-neuer-mitarbeiter');
    }
}