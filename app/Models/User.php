<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    /**
     * Get all roles assigned to this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()
            ->whereIn('name', $roleNames)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get all permissions from all user's roles
     */
    public function getAllPermissions(): Collection
    {
        return $this->roles()
            ->where('roles.is_active', true)
            ->with(['permissions' => function ($query) {
                $query->where('permissions.is_active', true);
            }])
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    /**
     * Get all permission keys from all user's roles
     */
    public function getAllPermissionKeys(): array
    {
        return $this->getAllPermissions()
            ->pluck('key')
            ->toArray();
    }

    /**
     * Check if user has a specific permission
     * This is the core method for permission checking
     */
    public function hasPermission(string $permissionKey): bool
    {
        return $this->roles()
            ->where('roles.is_active', true)
            ->whereHas('permissions', function ($query) use ($permissionKey) {
                $query->where('permissions.key', $permissionKey)
                    ->where('permissions.is_active', true);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionKeys): bool
    {
        return $this->roles()
            ->where('roles.is_active', true)
            ->whereHas('permissions', function ($query) use ($permissionKeys) {
                $query->whereIn('permissions.key', $permissionKeys)
                    ->where('permissions.is_active', true);
            })
            ->exists();
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissionKeys): bool
    {
        $userPermissionKeys = $this->getAllPermissionKeys();

        foreach ($permissionKeys as $key) {
            if (!in_array($key, $userPermissionKeys)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assign a role to this user
     */
    public function assignRole(Role $role): void
    {
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove a role from this user
     */
    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role->id);
    }

    /**
     * Sync roles to this user
     */
    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    /**
     * Get menus accessible by this user
     */
    public function getAccessibleMenus(): Collection
    {
        return Menu::getMenusForUser($this);
    }
}
