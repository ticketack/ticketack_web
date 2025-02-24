<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateEquipmentPermissions extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Liste des permissions à mettre à jour
        $permissions = [
            'equipements.view' => 'equipment.view',
            'equipements.create' => 'equipment.create',
            'equipements.edit' => 'equipment.edit',
            'equipements.delete' => 'equipment.delete',
        ];

        foreach ($permissions as $old => $new) {
            DB::table('permissions')
                ->where('name', $old)
                ->update(['name' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Liste des permissions à restaurer
        $permissions = [
            'equipment.view' => 'equipements.view',
            'equipment.create' => 'equipements.create',
            'equipment.edit' => 'equipements.edit',
            'equipment.delete' => 'equipements.delete',
        ];

        foreach ($permissions as $old => $new) {
            DB::table('permissions')
                ->where('name', $old)
                ->update(['name' => $new]);
        }
    }
}
