<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {user_id? : ID de l\'utilisateur} {--type=test : Type de notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer une notification de test à un utilisateur';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $userId = $this->argument('user_id');
        $type = $this->option('type');
        
        // Si aucun ID d'utilisateur n'est fourni, demander à l'utilisateur de choisir
        if (!$userId) {
            $users = User::all(['id', 'name', 'email']);
            
            if ($users->isEmpty()) {
                $this->error('Aucun utilisateur trouvé dans la base de données.');
                return 1;
            }
            
            $this->info('Utilisateurs disponibles:');
            foreach ($users as $user) {
                $this->line(" [{$user->id}] {$user->name} ({$user->email})");
            }
            
            $userId = $this->ask('Entrez l\'ID de l\'utilisateur pour envoyer la notification de test');
        }
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Utilisateur avec l'ID {$userId} non trouvé.");
            return 1;
        }
        
        $content = "Ceci est une notification de test envoyée le " . now()->format('d/m/Y à H:i:s');
        
        $result = $notificationService->sendNotification(
            $user,
            $type,
            $content,
            ['test' => true, 'command' => 'notification:test']
        );
        
        if ($result['success']) {
            $this->info("Notification de test envoyée à {$user->name} ({$user->email})");
            
            foreach ($result['results'] as $channel => $log) {
                $status = $log->status === 'sent' ? '<fg=green>envoyé</>' : '<fg=red>échec</>';
                $this->line(" - Canal {$channel}: {$status}");
            }
            
            return 0;
        } else {
            $this->error("Échec de l'envoi de la notification: " . $result['error']);
            return 1;
        }
    }
}
