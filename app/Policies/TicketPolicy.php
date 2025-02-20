<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasPermissionTo('tickets.view')) {
            // Si le ticket est public, tous les utilisateurs avec la permission peuvent le voir
            if ($ticket->is_public) {
                return true;
            }

            // Si le ticket est privé, seuls l'admin, le créateur et l'assigné peuvent le voir
            return $user->hasRole('admin') || 
                   $user->id === $ticket->created_by || 
                   $user->id === $ticket->assigned_to;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('tickets.create');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasPermissionTo('tickets.edit')) {
            return $user->hasRole('admin') || 
                   $user->id === $ticket->created_by || 
                   $user->id === $ticket->assigned_to;
        }
        return false;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasPermissionTo('tickets.delete');
    }
}
