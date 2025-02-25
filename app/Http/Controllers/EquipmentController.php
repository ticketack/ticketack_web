<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class EquipmentController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');
        $equipment = [];

        if (strlen($query) >= 3) {
            $equipment = Equipment::where('designation', 'like', "%{$query}%")
                ->orWhere('serial_number', 'like', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function ($equipment) {
                    return [
                        'id' => $equipment->id,
                        'text' => $equipment->designation . ' (' . $equipment->serial_number . ')',
                    ];
                });
        }

        return response()->json(['results' => $equipment]);
    }

    public function index(): Response
    {
        $equipment = Equipment::with('allChildren')
            ->whereNull('parent_id')
            ->get();

        $allEquipment = Equipment::all();

        return Inertia::render('Equipment/Index', [
            'equipment' => $equipment,
            'allEquipements' => $allEquipment,
            'translations' => [
                'pages' => trans('pages'),
                'equipment' => trans('equipment'),
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'pagination' => trans('pagination'),
                'passwords' => trans('passwords'),
                'validation' => trans('validation'),
                'permissions' => trans('permissions')
            ]
        ]);
    }

    public function create(): Response
    {
        $equipment = Equipment::all();
        return Inertia::render('Equipment/Create', [
            'allEquipment' => $equipment,
            'translations' => [
                'pages' => trans('pages'),
                'equipment' => trans('equipment'),
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'pagination' => trans('pagination'),
                'passwords' => trans('passwords'),
                'validation' => trans('validation'),
                'permissions' => trans('permissions')
            ]
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'icone' => 'nullable|integer',
            'parent_id' => 'nullable|exists:equipment,id'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipment', 's3');
            $validated['image'] = Storage::disk('s3')->url($path);
        }

        Equipment::create($validated);

        return redirect()->route('equipment.index')
            ->with('message', 'Équipement créé avec succès.');
    }

    public function edit(Equipment $equipment): Response
    {
        $equipmentList = Equipment::where('id', '!=', $equipment->id)->get();
        return Inertia::render('Equipment/Edit', [
            'equipment' => $equipment,
            'allEquipment' => $equipmentList,
            'translations' => [
                'pages' => trans('pages'),
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'pagination' => trans('pagination'),
                'passwords' => trans('passwords'),
                'validation' => trans('validation'),
                'permissions' => trans('permissions'),
                'equipment' => trans('equipment')
            ]
        ]);
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'icone' => 'nullable|integer',
            'parent_id' => 'nullable|exists:equipment,id'
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($equipment->image) {
                $oldPath = str_replace(Storage::disk('s3')->url(''), '', $equipment->image);
                Storage::disk('s3')->delete($oldPath);
            }

            $path = $request->file('image')->store('equipment', 's3');
            $validated['image'] = Storage::disk('s3')->url($path);
        }

        $equipment->update($validated);

        return redirect()->route('equipment.index')
            ->with('message', 'Équipement mis à jour avec succès.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        // Supprimer l'image du bucket si elle existe
        if ($equipment->image) {
            $path = str_replace(Storage::disk('s3')->url(''), '', $equipment->image);
            Storage::disk('s3')->delete($path);
        }

        $equipment->delete();

        return redirect()->route('equipment.index')
            ->with('message', 'Équipement supprimé avec succès.');
    }

    public function deleteImage(Equipment $equipment): RedirectResponse
    {
        if ($equipment->image) {
            $path = str_replace(Storage::disk('s3')->url(''), '', $equipment->image);
            Storage::disk('s3')->delete($path);
            $equipment->update(['image' => null]);
        }

        return redirect()->route('equipment.edit', $equipment)
            ->with('message', 'Image supprimée avec succès.');
    }
}
