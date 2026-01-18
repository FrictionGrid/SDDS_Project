<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'icon',
        'permission_key',
        'parent_id',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get the permission required for this menu
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_key', 'key');
    }

    /**
     * Get the parent menu
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get child menus (submenus)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Get active child menus
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Scope to get only active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only parent menus (no parent_id)
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get menus ordered by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get hierarchical menu structure
     */
    public static function getMenuTree()
    {
        return static::active()
            ->parent()
            ->ordered()
            ->with(['activeChildren' => function ($query) {
                $query->ordered();
            }])
            ->get();
    }

    /**
     * Get menus that user can access based on their roles
     */
    public static function getMenusForUser(User $user)
    {
        // Get all permission keys that user has
        $permissionKeys = $user->getAllPermissionKeys();

        // Get all active menus that match those permissions
        return static::active()
            ->whereIn('permission_key', $permissionKeys)
            ->parent()
            ->ordered()
            ->with(['activeChildren' => function ($query) use ($permissionKeys) {
                $query->whereIn('permission_key', $permissionKeys)->ordered();
            }])
            ->get();
    }
}
