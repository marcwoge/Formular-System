<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccessControlSeeder::class,
        ]);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.local'],
            [
                'name' => 'FormsHub Admin',
                'display_name' => 'FormsHub Admin',
                'login_name' => 'admin',
                'password' => 'admin123!',
                'status' => 'active',
                'is_guest' => false,
            ]
        );

        $adminRoleId = \App\Models\Role::query()->where('slug', 'superadmin')->value('id');
        if ($adminRoleId) {
            $admin->roles()->syncWithoutDetaching([$adminRoleId]);
        }
    }
}