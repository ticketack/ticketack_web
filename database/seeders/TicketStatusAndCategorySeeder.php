<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketStatusAndCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Création des statuts par défaut
        $statuses = [
            [
                'name' => 'Nouveau',
                'color' => '#3498db',
                'icon' => 'inbox',
                'is_default' => true,
                'is_closed' => false,
                'order' => 1
            ],
            [
                'name' => 'En cours',
                'color' => '#f1c40f',
                'icon' => 'clock',
                'is_default' => false,
                'is_closed' => false,
                'order' => 2
            ],
            [
                'name' => 'En attente',
                'color' => '#e67e22',
                'icon' => 'pause',
                'is_default' => false,
                'is_closed' => false,
                'order' => 3
            ],
            [
                'name' => 'Résolu',
                'color' => '#2ecc71',
                'icon' => 'check',
                'is_default' => false,
                'is_closed' => true,
                'order' => 4
            ],
            [
                'name' => 'Fermé',
                'color' => '#95a5a6',
                'icon' => 'x-circle',
                'is_default' => false,
                'is_closed' => true,
                'order' => 5
            ]
        ];

        foreach ($statuses as $status) {
            TicketStatus::create($status);
        }

        // Création des catégories par défaut
        $categories = [
            [
                'name' => 'Bug',
                'description' => 'Problème technique à résoudre',
                'color' => '#e74c3c',
                'icon' => 'bug',
                'order' => 1
            ],
            [
                'name' => 'Support',
                'description' => 'Demande d\'assistance',
                'color' => '#3498db',
                'icon' => 'question',
                'order' => 2
            ],
            [
                'name' => 'Amélioration',
                'description' => 'Suggestion d\'amélioration',
                'color' => '#2ecc71',
                'icon' => 'lightbulb',
                'order' => 3
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Tâche de maintenance',
                'color' => '#f1c40f',
                'icon' => 'wrench',
                'order' => 4
            ]
        ];

        foreach ($categories as $category) {
            TicketCategory::create($category);
        }
    }
}
