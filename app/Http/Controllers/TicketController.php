<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $query = Ticket::query()
            ->with(['category', 'status', 'creator', 'assignee', 'equipement']);

        // Si l'utilisateur est un "tiers", ne montrer que ses tickets
        if ($user->hasRole('tiers')) {
            $query->where('created_by', $user->id);
        }

        $tickets = $query->latest()->paginate(10);

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'categories' => TicketCategory::orderBy('order')->get(),
            'statuses' => TicketStatus::orderBy('order')->get(),
            'equipements' => Auth::user()->equipements,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category_id' => 'required|exists:ticket_categories,id',
            'equipement_id' => 'nullable|exists:equipements,id',
            'due_date' => 'nullable|date',
        ]);

        $status = TicketStatus::where('is_default', true)->first();

        $ticket = Ticket::create([
            ...$validated,
            'status_id' => $status->id,
            'created_by' => Auth::id(),
        ]);

        $ticket->addLog('created', 'Ticket créé');

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket créé avec succès.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'status', 'creator', 'assignee', 'equipement', 'logs.user']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::orderBy('order')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'status_id' => 'sometimes|required|exists:ticket_statuses,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'sometimes|required|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
        ]);

        $oldStatus = $ticket->status;
        
        $ticket->update($validated);

        if ($request->has('status_id') && $oldStatus->id !== $validated['status_id']) {
            $newStatus = TicketStatus::find($validated['status_id']);
            $ticket->addLog('status_changed', "Statut changé de {$oldStatus->name} à {$newStatus->name}");
        }

        if ($request->has('assigned_to')) {
            $ticket->addLog('assigned', "Ticket assigné à " . ($ticket->assignee->name ?? 'personne'));
        }

        return back()->with('success', 'Ticket mis à jour avec succès.');
    }
}
