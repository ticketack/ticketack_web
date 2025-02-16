<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class EquipementController extends Controller
{
    public function index(): Response
    {
        $equipements = Equipement::with('allChildren')
            ->whereNull('parent_id')
            ->get();

        $allEquipements = Equipement::all();

        return Inertia::render('Equipements/Index', [
            'equipements' => $equipements,
            'allEquipements' => $allEquipements,
            'translations' => [
                'pages' => trans('pages'),
                'equipements' => trans('equipements'),
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
        $equipements = Equipement::all();
        return Inertia::render('Equipements/Create', [
            'equipements' => $equipements,
            'translations' => [
                'pages' => trans('pages'),
                'equipements' => trans('equipements'),
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
            'parent_id' => 'nullable|exists:equipements,id'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipements', 's3');
            $validated['image'] = Storage::disk('s3')->url($path);
        }

        Equipement::create($validated);

        return redirect()->route('equipements.index')
            ->with('message', 'Équipement créé avec succès.');
    }

    public function edit(Equipement $equipement): Response
    {
        $equipements = Equipement::where('id', '!=', $equipement->id)->get();
        return Inertia::render('Equipements/Edit', [
            'equipement' => $equipement,
            'equipements' => $equipements,
            'translations' => [
                'pages' => trans('pages'),
                'menu' => trans('menu'),
                'auth' => trans('auth'),
                'pagination' => trans('pagination'),
                'passwords' => trans('passwords'),
                'validation' => trans('validation'),
                'permissions' => trans('permissions'),
                'equipements' => trans('equipements')
            ]
        ]);
    }

    public function update(Request $request, Equipement $equipement): RedirectResponse
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'icone' => 'nullable|integer',
            'parent_id' => 'nullable|exists:equipements,id'
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($equipement->image) {
                $oldPath = str_replace(Storage::disk('s3')->url(''), '', $equipement->image);
                Storage::disk('s3')->delete($oldPath);
            }

            $path = $request->file('image')->store('equipements', 's3');
            $validated['image'] = Storage::disk('s3')->url($path);
        }

        $equipement->update($validated);

        return redirect()->route('equipements.index')
            ->with('message', 'Équipement mis à jour avec succès.');
    }

    public function destroy(Equipement $equipement): RedirectResponse
    {
        // Supprimer l'image du bucket si elle existe
        if ($equipement->image) {
            $path = str_replace(Storage::disk('s3')->url(''), '', $equipement->image);
            Storage::disk('s3')->delete($path);
        }

        $equipement->delete();

        return redirect()->route('equipements.index')
            ->with('message', 'Équipement supprimé avec succès.');
    }

    public function deleteImage(Equipement $equipement): RedirectResponse
    {
        if ($equipement->image) {
            $path = str_replace(Storage::disk('s3')->url(''), '', $equipement->image);
            Storage::disk('s3')->delete($path);
            $equipement->update(['image' => null]);
        }

        return redirect()->route('equipements.edit', $equipement)
            ->with('message', 'Image supprimée avec succès.');
    }
}
