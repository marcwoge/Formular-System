<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_connectors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('default')->unique();
            $table->string('driver')->default('license_server');
            $table->string('base_url')->nullable();
            $table->string('product_name')->default('FormsHub');
            $table->string('product_code')->default('formshub');
            $table->text('activation_key')->nullable();
            $table->string('installation_id')->nullable();
            $table->string('fingerprint_hash', 128)->nullable();
            $table->string('activation_id')->nullable();
            $table->longText('access_token')->nullable();
            $table->longText('refresh_token')->nullable();
            $table->json('last_state')->nullable();
            $table->string('last_status')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->timestamp('last_validated_at')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamp('last_error_at')->nullable();
            $table->text('last_error')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();
        });

        Schema::create('license_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_connector_id')->constrained('license_connectors')->cascadeOnDelete();
            $table->string('mode')->default('free_fallback');
            $table->string('edition')->default('free');
            $table->string('result_status')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->string('reason')->nullable();
            $table->json('state')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('validated_at');
        });

        Schema::create('license_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_connector_id')->constrained('license_connectors')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('source')->default('license_server');
            $table->boolean('is_enabled')->default(true);
            $table->json('payload')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['license_connector_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_features');
        Schema::dropIfExists('license_validations');
        Schema::dropIfExists('license_connectors');
    }
};