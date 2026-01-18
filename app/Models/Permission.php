<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'group',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->withTimestamps();
    }

    /**
     * Get all menus that require this permission
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'permission_key', 'key');
    }

    /**
     * Get permissions grouped by group
     */
    public static function groupedPermissions()
    {
        return static::where('is_active', true)
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
    }
}
