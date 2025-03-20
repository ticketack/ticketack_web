<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter la nouvelle permission
        $permission = \Spatie\Permission\Models\Permission::findOrCreate('tickets.edit_author');
        
        // Attribuer la permission au rôle admin
        $adminRole = \Spatie\Permission\Models\Role::findByName('admin');
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la permission
        $permission = \Spatie\Permission\Models\Permission::findByName('tickets.edit_author');
        if ($permission) {
            $permission->delete();
        }
    }
};
