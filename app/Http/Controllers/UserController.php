<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct()
    {}

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
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->attach($validated['roles']);

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
            'roles.*' => 'exists:roles,id',
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

        $user->roles()->sync($validated['roles']);

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
