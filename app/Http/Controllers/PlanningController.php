<?php

namespace App\Http\Controllers;

use App\Models\TicketSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;
use Inertia\Response;

class PlanningController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:planning.view');
    }

    public function index(): Response
    {
        $schedules = TicketSchedule::with(['ticket.equipment', 'solver'])
            ->get()
            ->map(function ($schedule) {
                // Utiliser Carbon pour manipuler les dates
                $startTime = Carbon::parse($schedule->start_at);
                $endTime = $startTime->copy()->addMinutes($schedule->estimated_duration);
                
                return [
                    'id' => $schedule->ticket->id,
                    'title' => $schedule->ticket->title,
                    'start' => $startTime->toIso8601String(), // Format ISO 8601 avec fuseau horaire
                    'end' => $endTime->toIso8601String(), // Format ISO 8601 avec fuseau horaire
                    'assignedTo' => $schedule->solver ? [
                        'id' => $schedule->solver->id,
                        'name' => $schedule->solver->name,
                        'color' => $schedule->solver->color,
                    ] : null,
                    'equipment' => $schedule->ticket->equipment ? [
                        'id' => $schedule->ticket->equipment->id,
                        'designation' => $schedule->ticket->equipment->designation,
                    ] : null,
                    'url' => route('tickets.show', $schedule->ticket->id),
                ];
            });

        return Inertia::render('Planning/Index', [
            'events' => $schedules,
        ]);
    }
}
