<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'category',
        'status',
        'visibility_scope',
        'sensitivity',
        'allowed_contexts',
        'allow_user_edits',
        'allow_corrections',
        'workflow_definition',
        'retention_definition',
        'current_version_id',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'allowed_contexts' => 'array',
            'workflow_definition' => 'array',
            'retention_definition' => 'array',
            'allow_user_edits' => 'boolean',
            'allow_corrections' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function versions(): HasMany
    {
        return $this->hasMany(FormVersion::class);
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class, 'current_version_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(FormVersion::class)->latestOfMany('version_number');
    }
}