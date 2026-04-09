<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalIdentity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identity_provider_id',
        'external_identifier',
        'external_username',
        'attributes',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'last_synced_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function identityProvider(): BelongsTo
    {
        return $this->belongsTo(IdentityProvider::class);
    }
}
