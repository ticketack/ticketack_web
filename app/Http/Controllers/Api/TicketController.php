<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Display a listing of the tickets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['status', 'equipement', 'creator', 'assignees']);

        // Filtrage par statut
        if ($request->has('status')) {
            $query->whereHas('status', function($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        // Filtrage par priorité
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtrage par équipement
        if ($request->has('equipment_id')) {
            $query->where('equipement_id', $request->equipment_id);
        }

        // Filtrage par date de création
        if ($request->has('created_after')) {
            $query->whereDate('created_at', '>=', $request->created_after);
        }
        if ($request->has('created_before')) {
            $query->whereDate('created_at', '<=', $request->created_before);
        }

        // Recherche dans le titre et la description
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $tickets = $query->paginate($perPage);

        return response()->json($tickets);
    }

    /**
     * Store a newly created ticket in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,critical',
            'status_id' => 'required|exists:ticket_statuses,id',
            'equipement_id' => 'required|exists:equipements,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $ticket = Ticket::create($request->all());
        $ticket->load(['status', 'equipement', 'user']);

        return response()->json($ticket, Response::HTTP_CREATED);
    }

    /**
     * Display the specified ticket.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $ticket = Ticket::with(['status', 'equipement', 'user'])->find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($ticket);
    }

    /**
     * Update the specified ticket in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|string|in:low,medium,high,critical',
            'status_id' => 'sometimes|required|exists:ticket_statuses,id',
            'equipement_id' => 'sometimes|required|exists:equipements,id',
            'user_id' => 'sometimes|required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $ticket->update($request->all());
        $ticket->load(['status', 'equipement', 'user']);

        return response()->json($ticket);
    }

    /**
     * Remove the specified ticket from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], Response::HTTP_NOT_FOUND);
        }

        $ticket->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
