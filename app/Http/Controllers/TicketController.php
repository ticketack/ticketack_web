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
            ->with(['category', 'status', 'creator', 'assignees', 'equipment'])
            ->withLastActionDate()
            ->where('archived', false);

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
            $query->whereHas('assignees', function ($query) use ($request) {
                $query->where('user_id', $request->assigned_to);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        // Gestion du tri
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_dir', 'desc');
        
        // Liste des champs autorisés pour le tri
        $allowedSortFields = ['title', 'priority', 'status', 'created_at', 'due_date', 'last_action_at', 'category', 'equipment', 'author'];
        if (in_array($sortField, $allowedSortFields)) {
            if ($sortField === 'last_action_at') {
                // Tri spécial pour le champ calculé
                $query->orderByRaw("(SELECT MAX(created_at) FROM ticket_logs WHERE ticket_id = tickets.id) $sortDirection");
            } 
            // Tri pour les relations
            else if ($sortField === 'category') {
                $query->leftjoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
                      ->orderBy('ticket_categories.name', $sortDirection)
                      ->select('tickets.*');
            }
            // Pour le statut
            else if ($sortField === 'status') {
                $query->leftJoin('ticket_statuses', 'tickets.status_id', '=', 'ticket_statuses.id')
                    ->orderBy('ticket_statuses.name', $sortDirection)
                    ->select('tickets.*');
            }
            else if ($sortField === 'equipment') {
                $query->leftjoin('equipment', 'tickets.equipment_id', '=', 'equipment.id')
                      ->orderBy('equipment.designation', $sortDirection)
                      ->select('tickets.*');
            }
            // Pour la visibilité (is_public)
            else if ($sortField === 'visibility') {
                $query->orderBy('is_public', $sortDirection);
            }
            // Pour l'auteur
            else if ($sortField === 'author') {
                $query->leftjoin('users', 'tickets.created_by', '=', 'users.id')
                    ->orderBy('users.name', $sortDirection)
                    ->select('tickets.*');
            }
            else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            // Tri par défaut
            $query->latest();
        }

        $tickets = $query->paginate(50)->withQueryString();



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
                !$ticket->assignees()->where('user_id', $user->id)->exists()) {
                abort(403, 'Vous n\'avez pas accès à ce ticket.');
            }
        }

        $this->authorize('view', $ticket);

        $ticket->load(['category', 'status', 'creator', 'assignees', 'equipment', 'logs.user', 'documents', 'comments.user']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::orderBy('order')->get(),
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->load(['category', 'status', 'creator', 'assignees', 'equipment', 'documents']);

        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket,
            'statuses' => TicketStatus::orderBy('order')->get(),
            'categories' => TicketCategory::orderBy('order')->get(),
            'equipments' => \App\Models\Equipment::all(),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        \Log::info('Mise à jour du ticket - données reçues:', [
            'request_all' => $request->all(),
            'ticket_before' => $ticket->toArray(),
            'ticket_assignees_before' => $ticket->assignees->toArray()
        ]);

        // Si c'est une mise à jour partielle (juste le statut ou l'assignation)
        if ($request->has('status_id') && count($request->all()) <= 2) { // 2 pour status_id et _method
            $validated = $request->validate([
                'status_id' => 'required|exists:ticket_statuses,id',
            ]);

            // Gestion du changement de statut
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
            
            return redirect()->route('tickets.show', $ticket->id);
        }
        
        // Si c'est une mise à jour complète du ticket
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status_id' => 'required|exists:ticket_statuses,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'category_id' => 'required|exists:ticket_categories,id',
            'equipment_id' => 'nullable|exists:equipment,id',
            'is_public' => 'boolean',
            'due_date' => 'nullable|date',
            'documents.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
        ]);
        
        // Traiter la date d'échéance
        if (empty($validated['due_date'])) {
            $validated['due_date'] = null;
        }
        
        // Gestion du changement de statut
        $oldStatus = $ticket->status;
        $newStatus = TicketStatus::findOrFail($validated['status_id']);
        
        if ($oldStatus->id !== $newStatus->id) {
            $ticket->addLog('status_changed', "Statut changé de {$oldStatus->name} à {$newStatus->name}");
        }
        
        // Mise à jour des champs du ticket
        $ticket->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status_id' => $validated['status_id'],
            'priority' => $validated['priority'],
            'category_id' => $validated['category_id'],
            'equipment_id' => $validated['equipment_id'],
            'is_public' => $validated['is_public'],
            'due_date' => $validated['due_date'],
        ]);
        
        // Gestion des documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documentService = new TicketDocumentService();
                $documentService->storeDocument($ticket, $file);
            }
        }
        
        $ticket->addLog('updated', "Ticket mis à jour");
        
        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Ticket mis à jour avec succès');

        // L'assignation est maintenant gérée par les méthodes assign et unassign

        // Ce code ne sera jamais atteint en raison du return ci-dessus
        // return back()->with('success', 'Ticket mis à jour avec succès.');
    }

    /**
     * Assigner un utilisateur à un ticket
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $this->authorize('assign', $ticket);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);
            
            // Vérifier si l'utilisateur n'est pas déjà assigné
            if (!$ticket->assignees->contains($user->id)) {
                $ticket->assignees()->attach($user->id);
                $ticket->addLog('assigned', "Ticket assigné à {$user->name}");
                return back()->with('success', 'Utilisateur assigné avec succès');
            }

            return back()->with('error', 'L\'utilisateur est déjà assigné à ce ticket');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'assignation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de l\'assignation');
        }
    }

    /**
     * Retirer l'assignation d'un utilisateur d'un ticket
     */
    public function unassign(Ticket $ticket, User $user)
    {
        $this->authorize('assign', $ticket);

        try {
            if ($ticket->assignees->contains($user->id)) {
                $ticket->assignees()->detach($user->id);
                $ticket->addLog('unassigned', "Assignation de {$user->name} retirée");
                return back()->with('success', 'Assignation retirée avec succès');
            }

            return back()->with('error', 'L\'utilisateur n\'était pas assigné à ce ticket');

        } catch (\Exception $e) {
            \Log::error('Erreur lors du retrait de l\'assignation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors du retrait de l\'assignation');
        }
    }
    
    /**
     * Archiver un ticket
     */
    public function archive(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        
        try {
            $ticket->archived = true;
            $ticket->save();
            
            $ticket->addLog('archived', "Ticket archivé par " . Auth::user()->name);
            
            return back()->with('success', 'Ticket archivé avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'archivage du ticket:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de l\'archivage du ticket');
        }
    }
    
    /**
     * Désarchiver un ticket
     */
    public function unarchive(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        
        try {
            $ticket->archived = false;
            $ticket->save();
            
            $ticket->addLog('unarchived', "Ticket désarchivé par " . Auth::user()->name);
            
            return back()->with('success', 'Ticket désarchivé avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la désarchivage du ticket:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de la désarchivage du ticket');
        }
    }
    
    /**
     * Affiche la liste des tickets archivés
     */
    public function archived(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query()
            ->with(['category', 'status', 'creator', 'assignees', 'equipment'])
            ->where('archived', true);

        // Si l'utilisateur est un "tiers", ne montrer que ses tickets
        if ($user->hasRole('tiers')) {
            $query->where('created_by', $user->id);
        }

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
            $query->whereHas('assignees', function ($query) use ($request) {
                $query->where('user_id', $request->assigned_to);
            });
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

        return Inertia::render('Tickets/ArchivedTickets', [
            'tickets' => $tickets,
            'filters' => $request->all(),
            'statuses' => $statuses,
            'categories' => $categories,
            'users' => $users
        ]);
    }
}
