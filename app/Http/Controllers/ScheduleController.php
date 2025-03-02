<?php

namespace App\Http\Controllers;

use App\Models\TicketSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class ScheduleController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mettre à jour une planification existante
     */
    public function update(Request $request, $id): JsonResponse
    {
        $schedule = TicketSchedule::findOrFail($id);

        // Vérifier que l'utilisateur a les droits de modification
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'start_at' => 'required|date',
            'estimated_duration' => 'required|integer|min:1',
            'comments' => 'nullable|string',
        ]);

        // Conserver le fuseau horaire envoyé par le client
        // Les dates sont envoyées par le client avec le fuseau horaire local (format ISO 8601 avec +HH:MM)
        // Utiliser Carbon::parse sans conversion de fuseau horaire
        if (isset($validated['start_at'])) {
            $validated['start_at'] = Carbon::parse($validated['start_at']);
        }

        $schedule->update($validated);

        return response()->json($schedule->load('ticket'));
    }

    /**
     * Supprimer une planification
     */
    public function destroy($id): JsonResponse
    {
        $schedule = TicketSchedule::findOrFail($id);

        // Utiliser la politique au lieu de vérifier manuellement les autorisations
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
