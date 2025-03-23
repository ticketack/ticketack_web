<?php

namespace App\Observers;

use App\Models\TicketLog;
use App\Models\TicketStatus;
use Illuminate\Support\Facades\Log;

class TicketLogObserver
{
    /**
     * Handle the TicketLog "creating" event.
     */
    public function creating(TicketLog $ticketLog): void
    {
        // Vérifier si c'est un log de changement de statut et si les IDs de statut ne sont pas déjà définis
        if ($ticketLog->type === 'status_changed' && 
            (is_null($ticketLog->old_status_id) || is_null($ticketLog->new_status_id))) {
            
            // Extraire les noms de statut du message
            if (preg_match('/Statut chang[^ ]+ de ([^à]+) à ([^$]+)/', $ticketLog->message, $matches)) {
                $oldStatusName = trim($matches[1]);
                $newStatusName = trim($matches[2]);
                
                // Récupérer les IDs correspondants
                $oldStatus = TicketStatus::where('name', $oldStatusName)->first();
                $newStatus = TicketStatus::where('name', $newStatusName)->first();
                
                if ($oldStatus && $newStatus) {
                    $ticketLog->old_status_id = $oldStatus->id;
                    $ticketLog->new_status_id = $newStatus->id;
                } else {
                    Log::warning('Impossible de trouver les IDs de statut pour le message: ' . $ticketLog->message);
                }
            } else {
                Log::warning('Format de message de changement de statut non reconnu: ' . $ticketLog->message);
            }
        }
    }
}
