<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sysadmin = User::create([
            'name' => 'System Administrator',
            'email' => 'sysadmin@id-ingenierie.com',
            'email_verified_at' => now(),
            'password' => Hash::make('ID#Admin2025@Secure'),
        ]);

        // Attribution du rôle admin
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $sysadmin->roles()->attach($adminRole->id);
        }
    }
}
