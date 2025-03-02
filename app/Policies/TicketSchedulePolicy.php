<?php

namespace App\Policies;

use App\Models\TicketSchedule;
use App\Models\User;

class TicketSchedulePolicy
{
    public function update(User $user, TicketSchedule $schedule): bool
    {
        return $user->id === $schedule->solver_id || $user->hasRole('admin');
    }

    public function delete(User $user, TicketSchedule $schedule): bool
    {
        return $user->id === $schedule->solver_id || $user->hasRole('admin');
    }
    
    /**
     * Détermine si l'utilisateur peut supprimer le planning.
     * Cette méthode est utilisée par la méthode destroy du contrôleur.
     */
    public function destroy(User $user, TicketSchedule $schedule): bool
    {
        return $this->delete($user, $schedule);
    }
}
