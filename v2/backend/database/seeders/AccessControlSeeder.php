<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'System administration', 'slug' => 'system.admin', 'scope' => 'system'],
            ['name' => 'Forms manage', 'slug' => 'forms.manage', 'scope' => 'form'],
            ['name' => 'Forms publish', 'slug' => 'forms.publish', 'scope' => 'form'],
            ['name' => 'Submissions view all', 'slug' => 'submissions.view_all', 'scope' => 'submission'],
            ['name' => 'Submissions process', 'slug' => 'submissions.process', 'scope' => 'submission'],
            ['name' => 'Sensitive view', 'slug' => 'sensitive.view', 'scope' => 'security'],
            ['name' => 'Audit view', 'slug' => 'audit.view', 'scope' => 'security'],
            ['name' => 'Kiosk manage', 'slug' => 'kiosk.manage', 'scope' => 'kiosk'],
            ['name' => 'Plugins manage', 'slug' => 'plugins.manage', 'scope' => 'system'],
            ['name' => 'License manage', 'slug' => 'license.manage', 'scope' => 'system'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission + ['description' => null]
            );
        }

        $roleMap = [
            'superadmin' => ['System Administration', ['system.admin', 'forms.manage', 'forms.publish', 'submissions.view_all', 'submissions.process', 'sensitive.view', 'audit.view', 'kiosk.manage', 'plugins.manage', 'license.manage']],
            'formularadmin' => ['Formularadmin', ['forms.manage', 'forms.publish', 'submissions.view_all', 'submissions.process']],
            'fachadmin' => ['Fachadmin', ['submissions.view_all', 'submissions.process']],
            'bearbeiter' => ['Bearbeiter', ['submissions.process']],
            'auditor' => ['Auditor', ['audit.view', 'submissions.view_all']],
            'kiosk-benutzer' => ['Kiosk-Benutzer', []],
            'standardbenutzer' => ['Standardbenutzer', []],
            'gast' => ['Gast', []],
        ];

        foreach ($roleMap as $slug => [$name, $permissionSlugs]) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => null, 'is_system' => true]
            );

            $role->permissions()->sync(
                Permission::query()->whereIn('slug', $permissionSlugs)->pluck('id')->all()
            );
        }
    }
}