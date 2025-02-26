<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser les caches
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Création des permissions
        $permissions = [
            'dashboard.view',
            'equipment.view',
            'equipment.create',
            'equipment.edit',
            'equipment.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'settings.view',
            'settings.edit',
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            'tickets.delete',
            'update_ticket_status',
            'tickets.schedule',
            'tickets.assign',
            'solver_dashboard.view',
            'planning.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Création des rôles
        $admin = Role::findOrCreate('admin');
        $tiers = Role::findOrCreate('tiers');
        $solver = Role::findOrCreate('solver');

        // Supprimer toutes les permissions existantes
        $admin->revokePermissionTo(Permission::all());
        
        // Attribuer toutes les permissions au rôle admin
        $admin->givePermissionTo(Permission::all());

        // Les tiers peuvent voir le dashboard et gérer leurs tickets
        $tiers->givePermissionTo([
            'dashboard.view',
            'tickets.view',
            'tickets.create',
        ]);

        // Les solvers peuvent voir leur dashboard et planifier les tickets
        $solver->givePermissionTo([
            'dashboard.view',
            'solver_dashboard.view',
            'tickets.view',
            'tickets.schedule',
            'update_ticket_status',
        ]);

        // Attribution du rôle admin à l'utilisateur admin@example.com
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}
