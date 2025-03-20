<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les permissions
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Équipements
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',

            // Rôles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Utilisateurs
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Paramètres
            'settings.view',
            'settings.edit',

            // Tickets
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            'tickets.delete',
            'tickets.edit_author',
            'update_ticket_status',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Créer les rôles
        $admin = Role::findOrCreate('admin');
        $tiers = Role::findOrCreate('tiers');

        // Attribuer toutes les permissions à l'admin
        $admin->syncPermissions($permissions);

        // Attribuer les permissions de base aux tiers
        $tiers->syncPermissions([
            'dashboard.view',
            'tickets.view',
            'tickets.create',
        ]);
    }
}
