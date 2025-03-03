<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TimeTrackingPermissionSeeder extends Seeder
{
    public function run()
    {
        // Créer les permissions liées au time tracking
        $permissions = [
            'time_entries.view',
            'time_entries.create',
            'time_entries.edit',
            'time_entries.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Assigner les permissions au rôle admin
        $adminRole = Role::findByName('admin');
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assigner les permissions au rôle solver
        $solverRole = Role::findByName('solver');
        if ($solverRole) {
            $solverRole->givePermissionTo($permissions);
        }
    }
}
