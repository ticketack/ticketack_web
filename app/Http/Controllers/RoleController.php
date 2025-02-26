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
        $roles = Role::with('permissions')->get()->map(function ($role) {
            $rolePermissions = $role->permissions->pluck('name')->toArray();
            
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => Permission::all()->map(function ($permission) use ($rolePermissions) {
                    return [
                        'id' => $permission->id,
                        'name' => __('permissions.' . $permission->name),
                        'granted' => in_array($permission->name, $rolePermissions)
                    ];
                })
            ];
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        $permissions = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => __('permissions.' . $permission->name)
            ];
        });

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'permissions.*.id' => 'required|exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $permissionIds = collect($validated['permissions'])
            ->pluck('id')
            ->toArray();

        $role->syncPermissions($permissionIds);

        return redirect()->route('roles.index')
            ->with('message', 'Rôle créé avec succès.');
    }

    public function show(Role $role): Response
    {
        return Inertia::render('Roles/Show', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name
                    ];
                })
            ]
        ]);
    }

    public function edit(Role $role): Response
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        $formattedPermissions = $permissions->map(function ($permission) use ($rolePermissions) {
            return [
                'id' => $permission->id,
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
            'permissions.*.id' => 'exists:permissions,id',
            'permissions.*.granted' => 'boolean',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        // Filtrer les permissions accordées
        $grantedPermissions = collect($validated['permissions'])
            ->filter(function ($permission) {
                return $permission['granted'];
            })
            ->pluck('id')
            ->toArray();

        // Récupérer les permissions depuis la base de données
        $permissions = Permission::whereIn('id', $grantedPermissions)->get();

        $role->syncPermissions($permissions);

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
