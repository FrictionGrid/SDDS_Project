<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed RBAC data first (Roles, Permissions, Menus)
        $this->call(RBACSeeder::class);

        // Then seed Users and assign roles
        $this->call(UserSeeder::class);
    }
}
