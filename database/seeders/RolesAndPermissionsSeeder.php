<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Création des permissions
        $permissions = [
            'equipements.view',
            'equipements.create',
            'equipements.edit',
            'equipements.delete',
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
            'dashboard.view',
        ];

        // Chargement des traductions
        $translations = require base_path('lang/fr/permissions.php');

        foreach ($permissions as $name) {
            Permission::updateOrCreate(
                ['name' => $name],
                ['description' => $translations[$name] ?? $name]
            );
        }

        // Création des rôles
        $roles = [
            'admin' => 'Administrateur',
            'manager' => 'Gestionnaire',
            'viewer' => 'Lecteur',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Attribution des permissions aux rôles
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $viewerRole = Role::where('name', 'viewer')->first();

        // Admin a toutes les permissions
        $allPermissions = Permission::all();
        foreach ($allPermissions as $permission) {
            $permission->roles()->syncWithoutDetaching([
                $adminRole->id => ['granted' => true]
            ]);
        }

        // Manager peut tout faire sauf supprimer
        $managerPermissions = Permission::whereNotIn('name', ['equipements.delete'])->get();
        foreach ($managerPermissions as $permission) {
            $permission->roles()->syncWithoutDetaching([
                $managerRole->id => ['granted' => true]
            ]);
        }

        // Viewer peut seulement voir les équipements et le dashboard
        $viewerPermissions = Permission::whereIn('name', ['equipements.view', 'dashboard.view'])->get();
        foreach ($viewerPermissions as $permission) {
            $permission->roles()->syncWithoutDetaching([
                $viewerRole->id => ['granted' => true]
            ]);
        };

        // Attribution du rôle admin à l'utilisateur admin@example.com
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->roles()->attach($adminRole->id);
        }
    }
}
