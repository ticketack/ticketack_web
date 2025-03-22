<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EquipmentDocumentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Nous utilisons les permissions existantes pour les équipements
        // plutôt que de créer de nouvelles permissions spécifiques aux documents
        
        // Pour référence, voici comment nous aurions pu créer des permissions spécifiques
        // $permissions = [
        //     'view equipment documents',
        //     'create equipment documents',
        //     'edit equipment documents',
        //     'delete equipment documents',
        // ];
        //
        // foreach ($permissions as $permission) {
        //     Permission::findOrCreate($permission);
        // }
        //
        // $admin = Role::findByName('admin');
        // $admin->givePermissionTo($permissions);
        // 
        // $tiers = Role::findByName('tiers');
        // $tiers->givePermissionTo([
        //     'view equipment documents',
        // ]);
        
        // Actuellement, nous utilisons les permissions existantes dans la politique:
        // - equipment.view pour viewAny et view
        // - equipment.create pour create
        // - equipment.edit pour update et restore
        // - equipment.delete pour delete et forceDelete
    }
}
