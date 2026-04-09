<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_connector_id',
        'code',
        'name',
        'source',
        'is_enabled',
        'payload',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_enabled' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function connector(): BelongsTo
    {
        return $this->belongsTo(LicenseConnector::class, 'license_connector_id');
    }
}