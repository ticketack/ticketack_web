<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    /**
     * Store a newly created permission.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        $permission = Permission::create(['name' => $request->name]);
        return response()->json($permission, 201);
    }

    /**
     * Display the specified permission.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Update the specified permission.
     *
     * @param Request $request
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id
        ]);

        $permission->update(['name' => $request->name]);
        return response()->json($permission);
    }

    /**
     * Remove the specified permission.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null, 204);
    }
}
