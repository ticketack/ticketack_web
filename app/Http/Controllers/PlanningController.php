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
                $endTime = Carbon::parse($schedule->start_at)->addMinutes($schedule->estimated_duration);
                return [
                    'id' => $schedule->ticket->id,
                    'title' => $schedule->ticket->title,
                    'start' => $schedule->start_at,
                    'end' => $endTime->format('Y-m-d H:i:s'),
                    'assignedTo' => $schedule->solver ? [
                        'id' => $schedule->solver->id,
                        'name' => $schedule->solver->name,
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
