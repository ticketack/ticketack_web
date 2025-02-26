<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

use App\Models\Ticket;
use App\Models\TicketSchedule;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SolverDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:solver|admin']);
    }

    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Récupérer les tickets assignés au solver
        $assignedTickets = Ticket::with(['category', 'status'])
            ->whereHas('assignees', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer les planifications du solver avec toutes les relations nécessaires
        $schedules = TicketSchedule::with('ticket', 'ticket.status', 'ticket.category')
            ->where('solver_id', $user->id)
            ->orderBy('start_at')
            ->get();

        // Calculer les statistiques
        $totalEstimatedTime = $schedules->sum('estimated_duration');
        $ticketStats = $schedules->groupBy('ticket_id')
            ->map(function ($schedules) {
                return [
                    'total_time' => $schedules->sum('estimated_duration'),
                    'count' => $schedules->count(),
                ];
            });

        return Inertia::render('SolverDashboard/Index', [
            'assignedTickets' => $assignedTickets,
            'schedules' => $schedules,
            'stats' => [
                'totalEstimatedTime' => $totalEstimatedTime,
                'ticketStats' => $ticketStats,
            ],

        ]);
    }

    public function scheduleTicket(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'start_at' => 'required|date',
            'estimated_duration' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);
        $inProgressStatus = TicketStatus::where('name', 'En cours')->first();

        // Si le ticket est nouveau ou en attente, le passer en cours
        if ($ticket->status->name === 'Nouveau' || $ticket->status->name === 'En attente') {
            $ticket->update(['status_id' => $inProgressStatus->id]);
        }

        $schedule = TicketSchedule::create([
            'ticket_id' => $validated['ticket_id'],
            'solver_id' => $request->user()->id,
            'start_at' => $validated['start_at'],
            'estimated_duration' => $validated['estimated_duration'],
            'comments' => $validated['comments'],
        ]);

        return response()->json($schedule->load(['ticket' => function($query) {
            $query->with('status');
        }]));
    }

    public function updateSchedule(Request $request, TicketSchedule $schedule): JsonResponse
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'start_at' => 'required|date',
            'estimated_duration' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        $schedule->update($validated);

        return response()->json($schedule->load('ticket'));
    }

    public function deleteSchedule(TicketSchedule $schedule): JsonResponse
    {
        $this->authorize('delete', $schedule);
        
        $schedule->delete();
        
        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
