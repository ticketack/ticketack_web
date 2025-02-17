<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function __construct()
    {}

    public function index(): Response
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::with('permissions')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create', [
            'permissions' => Permission::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'permissions.*.id' => 'required|exists:permissions,id',
            'permissions.*.granted' => 'required|boolean',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        foreach ($validated['permissions'] as $permission) {
            $role->permissions()->attach($permission['id'], [
                'granted' => $permission['granted'],
            ]);
        }

        return redirect()->route('roles.index')
            ->with('message', 'Rôle créé avec succès.');
    }

    public function edit(Role $role): Response
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        $formattedPermissions = $permissions->map(function ($permission) use ($rolePermissions) {
            return [
                'id' => $permission->name,
                'name' => $permission->name,
                'granted' => in_array($permission->name, $rolePermissions)
            ];
        });

        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description
            ],
            'permissions' => $formattedPermissions
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Filtrer les permissions accordées
        $grantedPermissions = collect($request->permissions)
            ->filter(function ($permission) {
                return $permission['granted'];
            })
            ->pluck('id')
            ->toArray();

        $role->syncPermissions($grantedPermissions);

        return redirect()->route('roles.index')
            ->with('message', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Le rôle administrateur ne peut pas être supprimé.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('message', 'Rôle supprimé avec succès.');
    }
}
