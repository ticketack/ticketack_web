<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TiersUserSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = User::firstOrCreate(
            ['email' => 'tiers@id-ingenierie.com'],
            [
                'name' => 'Tiers User',
                'email_verified_at' => now(),
                'password' => Hash::make('Test123test'),
            ]
        );
        $tiersRole = Role::where('name', 'tiers')->first();
        if ($tiersRole) {
            $tiers->assignRole($tiersRole);
        }
    }
}
