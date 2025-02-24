<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques des utilisateurs
        $userCount = User::count();
        $usersWithMostAssignedTickets = User::withCount('assignedTickets as assigned_tickets_count')
            ->orderByDesc('assigned_tickets_count')
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'ticket_count' => $user->assigned_tickets_count
                ];
            });

        $usersWithMostCreatedTickets = User::withCount('tickets')
            ->orderByDesc('tickets_count')
            ->take(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'ticket_count' => $user->tickets_count
                ];
            });

        // Statistiques des équipements
        $equipmentCount = Equipment::count();

        // Équipements avec le plus/moins de tickets
        $equipmentStats = Equipment::withCount('tickets')
            ->get()
            ->sortByDesc('tickets_count')
            ->map(function ($equipment) {
                return [
                    'id' => $equipment->id,
                    'designation' => $equipment->designation,
                    'marque' => $equipment->marque,
                    'modele' => $equipment->modele,
                    'ticket_count' => $equipment->tickets_count
                ];
            });

        $mostTickets = $equipmentStats->take(3)->values();
        $leastTickets = $equipmentStats->reverse()->take(2)->values();

        // Statistiques des tickets
        $ticketStats = [
            'total' => Ticket::count(),
            'byStatus' => Ticket::select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->with('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status->name => $item->count];
                }),
            'byPriority' => Ticket::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->priority => $item->count];
                }),
            'byCategory' => Ticket::select('category_id', DB::raw('count(*) as count'))
                ->groupBy('category_id')
                ->with('category')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->category->name => $item->count];
                })
        ];

        // Données pour le graphique des 30 derniers jours
        $thirtyDaysAgo = now()->subDays(29)->startOfDay();
        $ticketsPerDay = Ticket::where('created_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count
                ];
            });

        // Créer un tableau avec tous les jours, même ceux sans tickets
        $chartData = collect(range(0, 29))->map(function ($days) use ($thirtyDaysAgo, $ticketsPerDay) {
            $date = $thirtyDaysAgo->copy()->addDays($days)->format('Y-m-d');
            return [
                'date' => $date,
                'count' => $ticketsPerDay->get($date)['count'] ?? 0
            ];
        });

        return Inertia::render('Dashboard', [
            'equipmentCount' => $equipmentCount,
            'userCount' => $userCount,
            'mostTickets' => $mostTickets,
            'leastTickets' => $leastTickets,
            'ticketStats' => $ticketStats,
            'chartData' => $chartData,
            'usersWithMostAssignedTickets' => $usersWithMostAssignedTickets,
            'usersWithMostCreatedTickets' => $usersWithMostCreatedTickets
        ]);
    }
}
