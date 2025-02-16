<?php

namespace Database\Seeders;

use App\Models\Equipement;
use Illuminate\Database\Seeder;

class EquipementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usine principale
        $usine = Equipement::create([
            'designation' => 'Usine de Production',
            'marque' => 'ID Ingénierie',
            'modele' => 'Site Principal',
            'image' => null,
            'icone' => 1
        ]);

        // Lignes de production
        $ligne1 = Equipement::create([
            'designation' => 'Ligne de Production 1',
            'marque' => 'Siemens',
            'modele' => 'LP2000',
            'image' => null,
            'icone' => 2,
            'parent_id' => $usine->id
        ]);

        $ligne2 = Equipement::create([
            'designation' => 'Ligne de Production 2',
            'marque' => 'ABB',
            'modele' => 'LP3000',
            'image' => null,
            'icone' => 2,
            'parent_id' => $usine->id
        ]);

        // Équipements de la ligne 1
        Equipement::create([
            'designation' => 'Robot de Soudure',
            'marque' => 'KUKA',
            'modele' => 'KR500',
            'image' => null,
            'icone' => 3,
            'parent_id' => $ligne1->id
        ]);

        Equipement::create([
            'designation' => 'Convoyeur',
            'marque' => 'Festo',
            'modele' => 'CT100',
            'image' => null,
            'icone' => 4,
            'parent_id' => $ligne1->id
        ]);

        // Équipements de la ligne 2
        Equipement::create([
            'designation' => 'Machine de Découpe',
            'marque' => 'Trumpf',
            'modele' => 'TruLaser 3030',
            'image' => null,
            'icone' => 5,
            'parent_id' => $ligne2->id
        ]);

        Equipement::create([
            'designation' => 'Station de Contrôle',
            'marque' => 'Zeiss',
            'modele' => 'Contura G2',
            'image' => null,
            'icone' => 6,
            'parent_id' => $ligne2->id
        ]);
    }
}
