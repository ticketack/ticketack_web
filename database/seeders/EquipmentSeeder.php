<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usine principale
        $usine = Equipment::create([
            'designation' => 'Optimachines',
            'marque' => 'NC',
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 1
        ]);

        // Lignes de production
        $ligne1 = Equipment::create([
            'designation' => 'Softwares',
            'marque' => null,
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 2,
            'parent_id' => $usine->id
        ]);

        $ligne2 = Equipment::create([
            'designation' => 'Servers',
            'marque' => null,
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 2,
            'parent_id' => $usine->id
        ]);

        // Équipements de la ligne 1
        Equipment::create([
            'designation' => 'Sage 100 Gestion commerciale',
            'marque' => 'Sage',
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 3,
            'parent_id' => $ligne1->id
        ]);

        Equipment::create([
            'designation' => 'Sage 100 Comptabilité',
            'marque' => 'Sage',
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 4,
            'parent_id' => $ligne1->id
        ]);

        Equipment::create([
            'designation' => 'Efficy',
            'marque' => null,
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 4,
            'parent_id' => $ligne1->id
        ]);

        Equipment::create([
            'designation' => 'MyReportBuilder',
            'marque' => 'MyPortal',
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 4,
            'parent_id' => $ligne1->id
        ]);

        // Équipements de la ligne 2
        Equipment::create([
            'designation' => 'VM-RDS1',
            'marque' => 'Virtual machine RDS 1',
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 5,
            'parent_id' => $ligne2->id
        ]);

        Equipment::create([
            'designation' => 'VM-RDS2',
            'marque' => 'Virtual machine RDS 2',
            'modele' => null,
            'serial_number' => null,
            'image' => null,
            'icone' => 6,
            'parent_id' => $ligne2->id
        ]);
    }
}
