<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'display_name',
        'login_name',
        'email',
        'password',
        'status',
        'department',
        'location',
        'manager_name',
        'organization_unit',
        'is_guest',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_guest' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_group_assignments')->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role_assignments')->withTimestamps();
    }

    public function externalIdentities(): HasMany
    {
        return $this->hasMany(ExternalIdentity::class);
    }

    public function allPermissions(): Collection
    {
        $directPermissions = $this->roles->loadMissing('permissions')
            ->flatMap(fn (Role $role) => $role->permissions);

        $groupPermissions = $this->groups->loadMissing('roles.permissions')
            ->flatMap(fn (Group $group) => $group->roles)
            ->flatMap(fn (Role $role) => $role->permissions);

        return $directPermissions
            ->merge($groupPermissions)
            ->unique('slug')
            ->values();
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug)
            || $this->groups->loadMissing('roles')->flatMap(fn (Group $group) => $group->roles)->contains('slug', $slug);
    }

    public function hasPermission(string $slug): bool
    {
        return $this->allPermissions()->contains('slug', $slug);
    }
}