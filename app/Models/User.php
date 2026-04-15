<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Traits\Blameable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Blameable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role',
        'status',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the tenant that owns the user.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the roles for the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Check if user has a specific role.
     * Checks both enum role field and Role model.
     */
    public function hasRole(string $roleName): bool
    {
        // First check enum role field for quick lookup
        if (strtolower($this->role) === strtolower($roleName)) {
            return true;
        }
        
        // Then check Role model
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has a specific permission.
     * Supports both module-level and action-level permissions.
     */
    public function hasPermission(string $permissionName): bool
    {
        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user has the permission through roles
        $hasPermission = $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->exists();

        if ($hasPermission) {
            return true;
        }

        // Check for module-level permission (e.g., if checking "orders.create", also check "orders")
        $parts = explode('.', $permissionName);
        if (count($parts) === 2) {
            $module = $parts[0];
            return $this->roles()
                ->whereHas('permissions', function ($query) use ($module) {
                    $query->where('name', $module);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Check if user is admin (using enum role).
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    /**
     * Check if user is manager (using enum role).
     */
    public function isManager(): bool
    {
        return strtolower($this->role) === 'manager';
    }

    /**
     * Check if user is staff (using enum role).
     */
    public function isStaff(): bool
    {
        return strtolower($this->role) === 'staff';
    }

    /**
     * Sync enum role with Role model.
     * Ensures user has the corresponding Role model role assigned.
     */
    public function syncRoleModel(): void
    {
        if (!$this->tenant_id) {
            return;
        }

        $roleModel = Role::where('tenant_id', $this->tenant_id)
            ->where('name', $this->role)
            ->first();

        if ($roleModel && !$this->hasRole($this->role)) {
            $this->roles()->syncWithoutDetaching([$roleModel->id]);
        }
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
