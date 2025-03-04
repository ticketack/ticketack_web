<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class TestNotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationType::create([
            'name' => 'Notification de test',
            'key' => 'test',
            'description' => 'Type de notification utilisé pour tester le système de notification',
        ]);
    }
}
