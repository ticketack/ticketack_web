<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

use App\Models\Ticket;
use App\Models\TicketSchedule;
use App\Models\TicketStatus;
use App\Services\NotificationService;
use Carbon\Carbon;
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
        $query = Ticket::with(['category', 'status', 'equipment', 'creator'])
            ->whereHas('assignees', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('status', function ($query) {
                $query->where('is_closed', false);
            });
            
        // Filtrer par recherche si une valeur est présente
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $assignedTickets = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                // Utiliser designation comme nom d'équipement
                $ticket->equipment_name = $ticket->equipment ? $ticket->equipment->designation : null;
                // Ajouter le nom de l'auteur en utilisant la relation creator
                $ticket->author_name = $ticket->creator ? $ticket->creator->name : null;
                return $ticket;
            });

        // Récupérer les planifications du solver avec toutes les relations nécessaires
        $schedules = TicketSchedule::with('ticket', 'ticket.status', 'ticket.category')
            ->where('solver_id', $user->id)
            ->whereHas('ticket.status', function ($query) {
                $query->where('is_closed', false);
            })
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
            'search' => $request->input('search', ''),
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

        // Conserver le fuseau horaire envoyé par le client
        // Les dates sont envoyées par le client avec le fuseau horaire local (format ISO 8601 avec +HH:MM)
        // Utiliser Carbon::parse sans conversion de fuseau horaire
        if (isset($validated['start_at'])) {
            $validated['start_at'] = Carbon::parse($validated['start_at']);
        }

        $schedule = TicketSchedule::create([
            'ticket_id' => $validated['ticket_id'],
            'solver_id' => $request->user()->id,
            'start_at' => $validated['start_at'],
            'estimated_duration' => $validated['estimated_duration'],
            'comments' => $validated['comments'],
        ]);
        
        // Ajouter un log pour la planification
        $ticket->addLog('scheduled', "Ticket planifié par {$request->user()->name}");
        
        // Envoyer des notifications aux personnes concernées
        $this->sendScheduleNotifications($ticket, $schedule);

        return response()->json($schedule->load(['ticket' => function($query) {
            $query->with('status');
        }]));
    }

    public function updateSchedule(Request $request, TicketSchedule $schedule): JsonResponse
    {
        // Vérifier que l'utilisateur est bien le solver assigné à ce schedule
        if ($schedule->solver_id !== $request->user()->id && !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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
    
    /**
     * Envoie des notifications aux personnes concernées lors de la planification d'un ticket
     *
     * @param Ticket $ticket Le ticket planifié
     * @param TicketSchedule $schedule La planification créée
     * @return void
     */
    private function sendScheduleNotifications(Ticket $ticket, TicketSchedule $schedule): void
    {
        $currentUser = auth()->user();
        $notificationService = app(NotificationService::class);
        $scheduledDate = $schedule->start_at->format('d/m/Y \à H:i');
        $content = "Le ticket #{$ticket->id} '{$ticket->title}' a été planifié pour le {$scheduledDate} par {$currentUser->name}";
        
        // Récupérer les destinataires (auteur et assignés)
        $recipients = collect();
        
        // Ajouter l'auteur s'il n'est pas la personne qui planifie
        if ($ticket->creator && $ticket->creator->id !== $currentUser->id) {
            $recipients->push($ticket->creator);
        }
        
        // Ajouter les assignés qui ne sont pas la personne qui planifie
        foreach ($ticket->assignees as $assignee) {
            if ($assignee->id !== $currentUser->id) {
                $recipients->push($assignee);
            }
        }
        
        // Envoyer la notification à chaque destinataire
        foreach ($recipients->unique('id') as $recipient) {
            $notificationService->sendNotification(
                $recipient,
                'ticket_scheduled',
                $content,
                [
                    'ticket_id' => $ticket->id,
                    'schedule_id' => $schedule->id,
                    'scheduled_by' => $currentUser->id,
                    'scheduled_by_name' => $currentUser->name,
                    'scheduled_date' => $schedule->start_at->toIso8601String(),
                    'estimated_duration' => $schedule->estimated_duration
                ]
            );
        }
    }
}
