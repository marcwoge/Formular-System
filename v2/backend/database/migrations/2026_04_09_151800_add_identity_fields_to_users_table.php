<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
            $table->string('login_name')->nullable()->after('display_name');
            $table->string('status')->default('active')->after('password');
            $table->string('department')->nullable()->after('status');
            $table->string('location')->nullable()->after('department');
            $table->string('manager_name')->nullable()->after('location');
            $table->string('organization_unit')->nullable()->after('manager_name');
            $table->boolean('is_guest')->default(false)->after('organization_unit');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->unique('login_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['login_name']);
            $table->dropColumn([
                'display_name',
                'login_name',
                'status',
                'department',
                'location',
                'manager_name',
                'organization_unit',
                'is_guest',
                'last_login_at',
            ]);
        });
    }
};
