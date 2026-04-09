<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicenseConnector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'driver',
        'base_url',
        'product_name',
        'product_code',
        'activation_key',
        'installation_id',
        'fingerprint_hash',
        'activation_id',
        'access_token',
        'refresh_token',
        'last_state',
        'last_status',
        'is_valid',
        'last_validated_at',
        'last_heartbeat_at',
        'last_error_at',
        'last_error',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'activation_key' => 'encrypted',
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'last_state' => 'array',
            'configuration' => 'array',
            'is_valid' => 'boolean',
            'last_validated_at' => 'datetime',
            'last_heartbeat_at' => 'datetime',
            'last_error_at' => 'datetime',
        ];
    }

    public function validations(): HasMany
    {
        return $this->hasMany(LicenseValidation::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(LicenseFeature::class);
    }
}