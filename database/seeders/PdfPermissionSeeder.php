<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PdfPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Créer la permission si elle n'existe pas
        Permission::firstOrCreate(['name' => 'tickets.generate_pdf']);

        // Assigner la permission au rôle admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo('tickets.generate_pdf');
        }
    }
}
