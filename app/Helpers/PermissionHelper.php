<?php

if (!function_exists('hasPermission')) {
    /**
     * Check if current authenticated user has a specific permission
     */
    function hasPermission(string $permissionKey): bool
    {
        return auth()->check() && auth()->user()->hasPermission($permissionKey);
    }
}

if (!function_exists('hasAnyPermission')) {
    /**
     * Check if current authenticated user has any of the given permissions
     */
    function hasAnyPermission(array $permissionKeys): bool
    {
        return auth()->check() && auth()->user()->hasAnyPermission($permissionKeys);
    }
}

if (!function_exists('hasAllPermissions')) {
    /**
     * Check if current authenticated user has all of the given permissions
     */
    function hasAllPermissions(array $permissionKeys): bool
    {
        return auth()->check() && auth()->user()->hasAllPermissions($permissionKeys);
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if current authenticated user has a specific role
     */
    function hasRole(string $roleName): bool
    {
        return auth()->check() && auth()->user()->hasRole($roleName);
    }
}

if (!function_exists('hasAnyRole')) {
    /**
     * Check if current authenticated user has any of the given roles
     */
    function hasAnyRole(array $roleNames): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole($roleNames);
    }
}

if (!function_exists('getUserMenus')) {
    /**
     * Get menus accessible by current authenticated user
     */
    function getUserMenus()
    {
        if (!auth()->check()) {
            return collect([]);
        }

        return auth()->user()->getAccessibleMenus();
    }
}
