<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct()
    {}

    public function search(Request $request)
    {
        if ($request->has('id')) {
            $user = User::find($request->input('id'));
            return response()->json([
                'result' => $user ? [
                    'id' => $user->id,
                    'text' => $user->name
                ] : null
            ]);
        }

        try {
            $search = $request->input('q');
            
            // Vérifier si l'utilisateur a la permission de gérer les tickets
            if (!auth()->user()->can('tickets.assign')) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Non autorisé à assigner des tickets');
            }

            // Récupérer les utilisateurs qui peuvent gérer les tickets
            $results = User::role(['admin', 'solver'])
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'email'])
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'text' => $user->name
                    ];
                });

            \Log::info('Recherche d\'utilisateurs', [
                'search' => $search,
                'results_count' => $results->count(),
                'user' => auth()->user()->name
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la recherche d\'utilisateurs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        return response()->json(['results' => $results]);
    }

    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'users' => User::with('roles')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['roles']);

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->email === 'admin@example.com') {
            return redirect()->route('users.index')
                ->with('error', 'L\'administrateur principal ne peut pas être modifié.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->email === 'admin@example.com') {
            return redirect()->route('users.index')
                ->with('error', 'L\'administrateur principal ne peut pas être supprimé.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('message', 'Utilisateur supprimé avec succès.');
    }
}
