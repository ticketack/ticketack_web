<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
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
        // Statistiques des équipements
        $equipmentCount = Equipement::count();
        $userCount = User::count();

        // Équipements avec le plus/moins de tickets
        $equipmentStats = Equipement::withCount('tickets')
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
            'chartData' => $chartData
        ]);
    }
}
