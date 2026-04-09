<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseValidation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'license_connector_id',
        'mode',
        'edition',
        'result_status',
        'is_valid',
        'reason',
        'state',
        'error_message',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'array',
            'is_valid' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }

    public function connector(): BelongsTo
    {
        return $this->belongsTo(LicenseConnector::class, 'license_connector_id');
    }
}