<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sysadmin = User::firstOrCreate(
            ['email' => 'sysadmin@id-ingenierie.com'],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('ID#Admin2025@Secure'),
            ]
        );

        // Attribution du rôle admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $sysadmin->assignRole($adminRole);
        }
    }
}
