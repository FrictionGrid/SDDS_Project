<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            [
                'key' => 'view_dashboard',
                'name' => 'View Dashboard',
                'description' => 'Permission to view dashboard',
                'group' => 'Dashboard',
            ],
            [
                'key' => 'view_customers',
                'name' => 'View Customers',
                'description' => 'Permission to view customers',
                'group' => 'Customer Management',
            ],
            [
                'key' => 'manage_customers',
                'name' => 'Manage Customers',
                'description' => 'Permission to create, edit, delete customers',
                'group' => 'Customer Management',
            ],
            [
                'key' => 'view_email_ai',
                'name' => 'View Email AI',
                'description' => 'Permission to access Email AI features',
                'group' => 'AI Features',
            ],
            [
                'key' => 'send_email_ai',
                'name' => 'Send Email AI',
                'description' => 'Permission to send emails using AI',
                'group' => 'AI Features',
            ],
            [
                'key' => 'view_documents',
                'name' => 'View Documents',
                'description' => 'Permission to view documents',
                'group' => 'Document Management',
            ],
            [
                'key' => 'manage_documents',
                'name' => 'Manage Documents',
                'description' => 'Permission to upload, edit, delete documents',
                'group' => 'Document Management',
            ],
            [
                'key' => 'manage_roles',
                'name' => 'Manage Roles',
                'description' => 'Permission to manage roles and permissions',
                'group' => 'System Administration',
            ],
            [
                'key' => 'manage_permissions',
                'name' => 'Manage Permissions',
                'description' => 'Permission to manage permissions',
                'group' => 'System Administration',
            ],
            [
                'key' => 'manage_menus',
                'name' => 'Manage Menus',
                'description' => 'Permission to manage menus',
                'group' => 'System Administration',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['key' => $permissionData['key']],
                $permissionData
            );
        }

        // Create Roles
        $ceoRole = Role::firstOrCreate(
            ['name' => 'ceo'],
            [
                'display_name' => 'CEO',
                'description' => 'Chief Executive Officer - Full access to all features',
                'is_active' => true,
            ]
        );

        $saleRole = Role::firstOrCreate(
            ['name' => 'sale'],
            [
                'display_name' => 'ฝ่ายขาย',
                'description' => 'Sales team - Access to dashboard, customers, and email AI',
                'is_active' => true,
            ]
        );

        // Assign all permissions to CEO role
        $allPermissions = Permission::all()->pluck('id')->toArray();
        $ceoRole->syncPermissions($allPermissions);

        // Assign specific permissions to Sale role
        $salePermissions = Permission::whereIn('key', [
            'view_dashboard',
            'view_customers',
            'manage_customers',
            'view_email_ai',
            'send_email_ai',
        ])->pluck('id')->toArray();
        $saleRole->syncPermissions($salePermissions);

        // Create Menus
        $menus = [
            [
                'name' => 'Dashboard',
                'url' => 'dashboard/sale',
                'icon' => 'dashboard',
                'permission_key' => 'view_dashboard',
                'parent_id' => null,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Customers',
                'url' => 'customers',
                'icon' => 'people',
                'permission_key' => 'view_customers',
                'parent_id' => null,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Email AI',
                'url' => 'email_ai',
                'icon' => 'email',
                'permission_key' => 'view_email_ai',
                'parent_id' => null,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Document',
                'url' => 'document',
                'icon' => 'folder',
                'permission_key' => 'view_documents',
                'parent_id' => null,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'System',
                'url' => '#',
                'icon' => 'settings',
                'permission_key' => 'manage_roles',
                'parent_id' => null,
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menuData) {
            $menu = Menu::firstOrCreate(
                ['url' => $menuData['url']],
                $menuData
            );

            // Create submenus for System menu
            if ($menuData['name'] === 'System') {
                $submenus = [
                    [
                        'name' => 'Roles',
                        'url' => 'roles',
                        'icon' => 'shield',
                        'permission_key' => 'manage_roles',
                        'parent_id' => $menu->id,
                        'order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Permissions',
                        'url' => 'permissions',
                        'icon' => 'key',
                        'permission_key' => 'manage_permissions',
                        'parent_id' => $menu->id,
                        'order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Menus',
                        'url' => 'menus',
                        'icon' => 'menu',
                        'permission_key' => 'manage_menus',
                        'parent_id' => $menu->id,
                        'order' => 3,
                        'is_active' => true,
                    ],
                ];

                foreach ($submenus as $submenuData) {
                    Menu::firstOrCreate(
                        ['url' => $submenuData['url']],
                        $submenuData
                    );
                }
            }
        }

        $this->command->info('RBAC data seeded successfully!');
        $this->command->info('- Permissions created: ' . Permission::count());
        $this->command->info('- Roles created: ' . Role::count());
        $this->command->info('- Menus created: ' . Menu::count());
        $this->command->info('');
        $this->command->info('Roles:');
        $this->command->info('  - CEO: All permissions');
        $this->command->info('  - Sale: Dashboard, Customers, Email AI');
    }
}
