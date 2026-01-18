<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Blade directive for checking permission
        Blade::if('hasPermission', function (string $permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // Blade directive for checking any permission
        Blade::if('hasAnyPermission', function (array $permissions) {
            return auth()->check() && auth()->user()->hasAnyPermission($permissions);
        });

        // Blade directive for checking all permissions
        Blade::if('hasAllPermissions', function (array $permissions) {
            return auth()->check() && auth()->user()->hasAllPermissions($permissions);
        });

        // Blade directive for checking role
        Blade::if('hasRole', function (string $role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // Blade directive for checking any role
        Blade::if('hasAnyRole', function (array $roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });
    }
}
