<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdentityProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_enabled',
        'configuration',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'configuration' => 'array',
        ];
    }

    public function externalIdentities(): HasMany
    {
        return $this->hasMany(ExternalIdentity::class);
    }
}
