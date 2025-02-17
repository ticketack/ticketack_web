<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\TicketDocument;
use App\Services\TicketDocumentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Response;

class TicketController extends Controller
{
    public function documents(Ticket $ticket): Response
    {
        return Inertia::render('Tickets/Steps/AddDocuments', [
            'ticket' => $ticket
        ]);
    }

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

        $tickets = $query->latest()->paginate(50);

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
        \Log::info('Création de ticket - données reçues:', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'category_id' => 'required|exists:ticket_categories,id',
            'equipement_id' => 'nullable|exists:equipements,id',
            'due_date' => 'nullable|date',
        ]);

        // Traiter la date d'échéance
        if (empty($validated['due_date'])) {
            unset($validated['due_date']);
        }

        $status = TicketStatus::where('is_default', true)->first();

        $ticket = Ticket::create([
            ...$validated,
            'status_id' => $status->id,
            'created_by' => Auth::id(),
        ]);

        $ticket->addLog('created', 'Ticket créé');

        \Log::info('Ticket créé avec succès:', ['ticket_id' => $ticket->id]);

        return redirect()->route('tickets.create')->with([
            'success' => 'Ticket créé avec succès',
            'ticket' => $ticket
        ]);
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'status', 'creator', 'assignee', 'equipement', 'logs.user', 'documents']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::orderBy('order')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        \Log::info('Mise à jour du ticket - données reçues:', $request->all());

        $validated = $request->validate([
            'status_id' => 'required|exists:ticket_statuses,id'
        ]);

        $oldStatus = $ticket->status;
        $newStatus = TicketStatus::findOrFail($validated['status_id']);
        
        if ($oldStatus->id !== $newStatus->id) {
            $ticket->update([
                'status_id' => $newStatus->id
            ]);

            $ticket->addLog('status_changed', "Statut changé de {$oldStatus->name} à {$newStatus->name}");
            
            \Log::info('Statut du ticket mis à jour:', [
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus->name,
                'new_status' => $newStatus->name
            ]);
        }

        return back()->with('success', 'Statut du ticket mis à jour avec succès.');
    }
}
