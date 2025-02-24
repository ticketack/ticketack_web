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
}
