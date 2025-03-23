<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TicketStatus;

class UpdateTicketLogsStatusIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket-logs:update-status-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour les colonnes old_status_id et new_status_id dans la table ticket_logs en se basant sur le message';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début de la mise à jour des IDs de statut dans les logs de tickets...');
        
        // Récupérer tous les statuts pour faire la correspondance nom -> id
        $statuses = TicketStatus::all()->pluck('id', 'name')->toArray();
        
        // Créer un tableau inversé pour la recherche par nom normalisé
        $normalizedStatuses = [];
        foreach ($statuses as $name => $id) {
            $normalizedName = $this->normalizeStatusName($name);
            $normalizedStatuses[$normalizedName] = $id;
        }
        
        // Récupérer tous les logs de changement de statut
        $logs = DB::table('ticket_logs')
            ->where('type', 'status_changed')
            ->whereNull('old_status_id')
            ->orWhereNull('new_status_id')
            ->get();
        
        $this->info("Traitement de {$logs->count()} logs de changement de statut...");
        
        $updatedCount = 0;
        $errorCount = 0;
        
        foreach ($logs as $log) {
            // Extraire les noms de statut du message
            if (preg_match('/Statut chang[^ ]+ de ([^?]+) [?à] ([^?]+)/', $log->message, $matches)) {
                $oldStatusName = trim($matches[1]);
                $newStatusName = trim($matches[2]);
                
                // Normaliser les noms de statut pour gérer les problèmes d'encodage
                $oldStatusNameNormalized = $this->normalizeStatusName($oldStatusName);
                $newStatusNameNormalized = $this->normalizeStatusName($newStatusName);
                
                // Récupérer les IDs correspondants
                $oldStatusId = $normalizedStatuses[$oldStatusNameNormalized] ?? null;
                $newStatusId = $normalizedStatuses[$newStatusNameNormalized] ?? null;
                
                if ($oldStatusId && $newStatusId) {
                    // Mettre à jour le log
                    DB::table('ticket_logs')
                        ->where('id', $log->id)
                        ->update([
                            'old_status_id' => $oldStatusId,
                            'new_status_id' => $newStatusId
                        ]);
                    
                    $updatedCount++;
                } else {
                    $this->error("Impossible de trouver les IDs pour les statuts: '{$oldStatusName}' -> '{$newStatusName}'");
                    $errorCount++;
                }
            } else {
                $this->warn("Format de message non reconnu: {$log->message}");
                $errorCount++;
            }
        }
        
        $this->info("Mise à jour terminée: {$updatedCount} logs mis à jour, {$errorCount} erreurs.");
    }
    
    /**
     * Normalise le nom du statut pour gérer les problèmes d'encodage
     */
    private function normalizeStatusName(string $name): string
    {
        // Remplacer les caractères problématiques
        $normalized = str_replace(['?', '�'], ['é', 'é'], $name);
        
        // Convertir en minuscules et supprimer les espaces
        return strtolower(preg_replace('/\s+/', '', $normalized));
    }
}
