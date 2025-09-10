<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SolverUserSeeder extends Seeder
{
    public function run(): void
    {
        $solver = User::firstOrCreate(
            ['email' => 'solver@id-ingenierie.com'],
            [
                'name' => 'Solver User',
                'email_verified_at' => now(),
                'password' => Hash::make('ID#Solver2025@Secure'),
            ]
        );
        $solverRole = Role::where('name', 'solver')->first();
        if ($solverRole) {
            $solver->assignRole($solverRole);
        }
    }
}
