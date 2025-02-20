<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer la nouvelle permission
        Permission::findOrCreate('tickets.assign');

        // Donner la permission aux rôles appropriés
        $admin = Role::findByName('admin');
        if ($admin) {
            $admin->givePermissionTo('tickets.assign');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la permission
        $permission = Permission::findByName('tickets.assign');
        if ($permission) {
            $permission->delete();
        }
    }
};
