<?php

namespace App\Http\Controllers;

use App\Models\TicketSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
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
        try {
            // Trouver le planning ou échouer
            $schedule = TicketSchedule::findOrFail($id);
            
            // Vérifier si l'utilisateur est autorisé à supprimer ce planning
            if (auth()->user()->id !== $schedule->solver_id && !auth()->user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            // Supprimer le planning
            $schedule->delete();
            
            return response()->json(['message' => 'Schedule deleted successfully']);
        } catch (\Exception $e) {
            // Journaliser l'erreur
            \Log::error('Error deleting schedule: ' . $e->getMessage(), [
                'id' => $id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json(['message' => 'Error deleting schedule: ' . $e->getMessage()], 500);
        }
    }
}
