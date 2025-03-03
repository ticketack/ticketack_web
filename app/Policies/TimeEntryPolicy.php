<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\Ticket;
use App\Models\User;

class TimeEntryPolicy
{
    /**
     * Détermine si l'utilisateur peut voir toutes les entrées de temps
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('time_entries.view') || $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut voir une entrée de temps spécifique
     */
    public function view(User $user, TimeEntry $timeEntry): bool
    {
        // L'utilisateur peut voir ses propres entrées de temps
        if ($timeEntry->user_id === $user->id) {
            return true;
        }

        // Les administrateurs et les utilisateurs avec la permission peuvent voir toutes les entrées
        return $user->hasPermissionTo('time_entries.view') || $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut créer des entrées de temps
     */
    public function create(User $user, ?Ticket $ticket = null): bool
    {
        // Vérifier si l'utilisateur a la permission générale
        $hasPermission = $user->hasPermissionTo('time_entries.create') || $user->hasRole('admin');
        
        if (!$hasPermission) {
            return false;
        }
        
        // Si un ticket est spécifié, vérifier si l'utilisateur est assigné à ce ticket
        if ($ticket) {
            return $ticket->assignees()->where('user_id', $user->id)->exists() || $user->hasRole('admin');
        }
        
        return true;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour une entrée de temps
     */
    public function update(User $user, TimeEntry $timeEntry): bool
    {
        // L'utilisateur peut modifier ses propres entrées de temps récentes (moins de 7 jours)
        if ($timeEntry->user_id === $user->id && $timeEntry->created_at->diffInDays(now()) < 7) {
            return true;
        }
        
        // Les administrateurs et les utilisateurs avec la permission peuvent modifier toutes les entrées
        return $user->hasPermissionTo('time_entries.edit') || $user->hasRole('admin');
    }

    /**
     * Détermine si l'utilisateur peut supprimer une entrée de temps
     */
    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        // L'utilisateur peut supprimer ses propres entrées de temps récentes (moins de 7 jours)
        if ($timeEntry->user_id === $user->id && $timeEntry->created_at->diffInDays(now()) < 7) {
            return true;
        }
        
        // Les administrateurs et les utilisateurs avec la permission peuvent supprimer toutes les entrées
        return $user->hasPermissionTo('time_entries.delete') || $user->hasRole('admin');
    }
}
