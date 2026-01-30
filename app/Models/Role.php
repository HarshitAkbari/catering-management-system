<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Blameable;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasTenant, Blameable;

    protected $fillable = [
        'tenant_id',
        'name',
        'display_name',
        'description',
        'permission_type',
        'write_permissions',
    ];

    protected function casts(): array
    {
        return [
            'write_permissions' => 'array',
        ];
    }

    /**
     * Get the tenant that owns the role.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the users that have this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Get the permissions for the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }

    /**
     * Get the menus for the role.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menu')
            ->withTimestamps();
    }

    /**
     * Check if role has a specific write permission.
     */
    public function hasWritePermission(string $permission): bool
    {
        if ($this->permission_type !== 'write') {
            return false;
        }

        $writePermissions = $this->write_permissions ?? [];
        return in_array($permission, $writePermissions);
    }

    /**
     * Check if role can perform a specific action.
     */
    public function canPerformAction(string $action): bool
    {
        if ($this->permission_type === 'read') {
            return $action === 'view';
        }

        if ($this->permission_type === 'write') {
            return $this->hasWritePermission($action);
        }

        return false;
    }
}

