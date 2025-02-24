<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketStatus;
use App\Models\TicketDocument;
use App\Models\User;
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

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query()
            ->with(['category', 'status', 'creator', 'assignee', 'equipment']);

        // Si l'utilisateur est un "tiers", ne montrer que ses tickets
        if ($user->hasRole('tiers')) {
            $query->where('created_by', $user->id);
        }
        // Pour les autres utilisateurs, montrer tous les tickets (même privés)
        // L'affichage sera géré côté frontend

        // Appliquer les filtres
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->latest()->paginate(50)->withQueryString();

        // Récupérer les données pour les filtres
        $statuses = TicketStatus::orderBy('order')->get();
        $categories = TicketCategory::orderBy('order')->get();
        $users = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'tiers');
        })->get();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->all(),
            'statuses' => $statuses,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    public function create()
    {
        return Inertia::render('Tickets/Create', [
            'categories' => TicketCategory::orderBy('order')->get(),
            'statuses' => TicketStatus::orderBy('order')->get(),
            'equipment' => Auth::user()->equipment,
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
            'equipment_id' => 'nullable|exists:equipment,id',
            'due_date' => 'nullable|date',
            'is_public' => 'boolean',
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

        return Inertia::render('Tickets/Create', [
            'ticket' => $ticket,
            'categories' => TicketCategory::orderBy('order')->get(),
            'statuses' => TicketStatus::orderBy('order')->get(),
            'equipment' => Auth::user()->equipment,
        ])->with('success', 'Ticket créé avec succès');
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur peut voir le ticket
        if (!$ticket->is_public) {
            // Si le ticket est privé, vérifier si l'utilisateur est autorisé
            if (!$user->hasRole('admin') && 
                $ticket->created_by !== $user->id && 
                $ticket->assigned_to !== $user->id) {
                abort(403, 'Vous n\'avez pas accès à ce ticket.');
            }
        }

        $this->authorize('view', $ticket);

        $ticket->load(['category', 'status', 'creator', 'assignee', 'equipment', 'logs.user', 'documents', 'comments.user']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::orderBy('order')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        \Log::info('Mise à jour du ticket - données reçues:', [
            'request_all' => $request->all(),
            'ticket_before' => $ticket->toArray(),
            'ticket_assignee_before' => $ticket->assignee ? $ticket->assignee->toArray() : null
        ]);

        $validated = $request->validate([
            'status_id' => 'sometimes|required|exists:ticket_statuses,id',
            'assigned_to' => 'sometimes|required|exists:users,id'
        ]);

        \Log::info('Données validées:', [
            'validated' => $validated,
            'has_assigned_to' => isset($validated['assigned_to']),
            'assigned_to_value' => $validated['assigned_to'] ?? null
        ]);

        // Gestion du changement de statut
        if (isset($validated['status_id'])) {
            $this->authorize('updateStatus', $ticket);
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
        }

        // Gestion de l'assignation
        if (isset($validated['assigned_to'])) {
            $this->authorize('assign', $ticket);
            try {
                \Log::info('Début assignation:', [
                    'ticket_id' => $ticket->id,
                    'assigned_to' => $validated['assigned_to'],
                    'current_assigned_to' => $ticket->assigned_to
                ]);

                $oldAssignee = $ticket->assignee;
                $newAssignee = User::findOrFail($validated['assigned_to']);

                if (!$oldAssignee || $oldAssignee->id !== $newAssignee->id) {
                    \DB::enableQueryLog();
                    
                    $ticket->assigned_to = $newAssignee->id;
                    $result = $ticket->save();

                    \Log::info('Queries exécutées:', [
                        'queries' => \DB::getQueryLog()
                    ]);

                    \Log::info('Résultat update:', [
                        'result' => $result,
                        'ticket_after_save' => $ticket->toArray(),
                        'assigned_to_after' => $ticket->fresh()->assigned_to
                    ]);

                    // Vérifions que la mise à jour a bien été faite
                    $ticket->refresh();
                    \Log::info('Après refresh:', [
                        'assigned_to' => $ticket->assigned_to,
                        'assignee' => $ticket->assignee ? $ticket->assignee->toArray() : null,
                        'ticket_full' => $ticket->toArray()
                    ]);

                    $oldAssigneeName = $oldAssignee ? $oldAssignee->name : 'personne';
                    $ticket->addLog('assigned', "Ticket réassigné de {$oldAssigneeName} à {$newAssignee->name}");
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'assignation:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }

        return back()->with('success', 'Ticket mis à jour avec succès.');
    }
}
