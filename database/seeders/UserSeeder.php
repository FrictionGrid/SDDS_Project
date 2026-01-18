<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User 1: CEO
        $ceoUser = User::firstOrCreate(
            ['email' => 'phumiphat.s@ku.th'],
            [
                'name' => 'Phumiphat (CEO)',
                'password' => bcrypt('Babie5555_'),
            ]
        );

        $ceoRole = Role::where('name', 'ceo')->first();
        if ($ceoRole) {
            $ceoUser->assignRole($ceoRole);
        }

        // User 2: Sale
        $saleUser = User::firstOrCreate(
            ['email' => 'phumiphat.s2310@gmail.com'],
            [
                'name' => 'Phumiphat (Sale)',
                'password' => bcrypt('Babie5555_'),
            ]
        );

        $saleRole = Role::where('name', 'sale')->first();
        if ($saleRole) {
            $saleUser->assignRole($saleRole);
        }

        $this->command->info('Users created successfully!');
        $this->command->info('');
        $this->command->info('CEO User:');
        $this->command->info('  Email: phumiphat.s@ku.th');
        $this->command->info('  Password: Babie5555_');
        $this->command->info('');
        $this->command->info('Sale User:');
        $this->command->info('  Email: phumiphat.s2310@gmail.com');
        $this->command->info('  Password: Babie5555_');
    }
}
