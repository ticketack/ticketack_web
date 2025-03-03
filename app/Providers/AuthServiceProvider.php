<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\TicketSchedule;
use App\Models\TimeEntry;
use App\Policies\TicketPolicy;
use App\Policies\TicketSchedulePolicy;
use App\Policies\TimeEntryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Ticket::class => TicketPolicy::class,
        TicketSchedule::class => TicketSchedulePolicy::class,
        TimeEntry::class => TimeEntryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
