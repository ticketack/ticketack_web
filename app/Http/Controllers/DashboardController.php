<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques des équipements
        $equipmentCount = Equipement::count();
        $latestEquipments = Equipement::latest()->take(5)->get();

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

        return Inertia::render('Dashboard', [
            'equipmentCount' => $equipmentCount,
            'latestEquipments' => $latestEquipments,
            'ticketStats' => $ticketStats
        ]);
    }
}
