<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notificationTypes = [
            [
                'name' => 'Création d\'un ticket',
                'key' => 'ticket_created',
                'description' => 'Notification envoyée lorsqu\'un nouveau ticket est créé',
            ],
            [
                'name' => 'Pointage sur un ticket',
                'key' => 'ticket_time_tracked',
                'description' => 'Notification envoyée lorsqu\'un pointage est effectué sur un ticket',
            ],
            [
                'name' => 'Commentaire sur un ticket',
                'key' => 'ticket_commented',
                'description' => 'Notification envoyée lorsqu\'un commentaire est ajouté à un ticket',
            ],
            [
                'name' => 'Changement de statut d\'un ticket',
                'key' => 'ticket_status_changed',
                'description' => 'Notification envoyée lorsque le statut d\'un ticket est modifié',
            ],
            [
                'name' => 'Assignation d\'un ticket',
                'key' => 'ticket_assigned',
                'description' => 'Notification envoyée lorsqu\'un ticket est assigné à un utilisateur',
            ],
            [
                'name' => 'Planification d\'un ticket',
                'key' => 'ticket_scheduled',
                'description' => 'Notification envoyée lorsqu\'un ticket est planifié',
            ],
        ];

        foreach ($notificationTypes as $type) {
            NotificationType::updateOrCreate(
                ['key' => $type['key']],
                $type
            );
        }
    }
}
