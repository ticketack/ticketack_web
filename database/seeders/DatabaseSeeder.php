<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Permissions et rôles d'abord
        $this->call(RolesAndPermissionsSeeder::class);

        // Ensuite l'utilisateur admin qui a besoin du rôle admin
        $this->call(AdminUserSeeder::class);

        // Puis les autres seeders
        $this->call([
            EquipmentSeeder::class,
            SettingsSeeder::class,
            TicketStatusAndCategorySeeder::class,
            TimeTrackingPermissionSeeder::class,
            NotificationTypeSeeder::class,
        ]);
    }
}
