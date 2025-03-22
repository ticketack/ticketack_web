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

    public function index(Request $request): Response
    {
        $search = $request->input('search', '');
        
        // Récupérer tous les équipements
        $allEquipmentItems = Equipment::all();
        
        // Construire un tableau d'équipements organisé par parent_id
        $equipmentByParent = [];
        foreach ($allEquipmentItems as $item) {
            $parentId = $item->parent_id ?: 'root';
            if (!isset($equipmentByParent[$parentId])) {
                $equipmentByParent[$parentId] = [];
            }
            $equipmentByParent[$parentId][] = $item;
        }
        
        // Récupérer les équipements de premier niveau (sans parent)
        $query = Equipment::whereNull('parent_id');
        
        // Log pour débogage
        \Log::info('Chargement des équipements avec leurs enfants (nouvelle approche)');
        
        // Récupérer les IDs des équipements correspondant à la recherche (si recherche active)
        $matchingIds = [];
        $parentIds = []; // Pour stocker les IDs des parents des équipements correspondants
        
        if (!empty($search)) {
            // Rechercher dans tous les équipements avec une recherche insensible à la casse
            $matchingEquipment = Equipment::where(function($query) use ($search) {
                $query->whereRaw('LOWER(designation) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(serial_number) LIKE ?', ['%' . strtolower($search) . '%']);
            })->get();
            
            // Collecter les IDs des équipements correspondants
            $matchingIds = $matchingEquipment->pluck('id')->toArray();
            
            // Trouver tous les parents des équipements correspondants
            $this->findAllParents($matchingEquipment, $parentIds);
            
            // Log pour débogage
            \Log::info('Recherche d\'\u00e9quipements pour: ' . $search);
            \Log::info('Nombre d\'\u00e9quipements trouvés: ' . count($matchingIds));
            \Log::info('Nombre de parents trouvés: ' . count($parentIds));
        }
        
        // Récupérer tous les équipements de premier niveau
        $equipment = $query->get();
        
        // Vérifier que les enfants sont bien chargés
        foreach ($equipment as $item) {
            $childCount = $item->all_children ? count($item->all_children) : 0;
            \Log::info("\u00c9quipement {$item->id} ({$item->designation}) a {$childCount} enfants directs");
        }
        
        // Fonction récursive pour convertir les équipements au format PrimeVue Tree
        $formatForPrimeVue = function($equipmentItem) use (&$formatForPrimeVue, $matchingIds, $parentIds, $search, $equipmentByParent) {
            // Vérifier si cet équipement correspond directement à la recherche
            $directMatch = in_array($equipmentItem->id, $matchingIds);
            
            // Vérifier si c'est un parent d'un équipement correspondant
            $isParent = in_array($equipmentItem->id, $parentIds);
            
            // Créer le noeud au format PrimeVue
            $node = [
                'key' => $equipmentItem->id,
                'label' => $equipmentItem->designation,
                'data' => [
                    'id' => $equipmentItem->id,
                    'designation' => $equipmentItem->designation,
                    'parent_id' => $equipmentItem->parent_id,
                    'matches_search' => $directMatch,
                    'is_parent' => $isParent,
                    'created_at' => $equipmentItem->created_at,
                    'updated_at' => $equipmentItem->updated_at
                ],
                'styleClass' => $directMatch ? 'p-highlight' : ($isParent ? 'p-tree-parent' : ''),
                'expanded' => $directMatch || $isParent || !empty($search)
            ];
            
            // Récupérer les enfants depuis le tableau organisé
            $childrenItems = $equipmentByParent[$equipmentItem->id] ?? [];
            
            // Traiter les enfants s'ils existent
            if (!empty($childrenItems)) {
                \Log::info("Formatage des enfants pour l'équipement {$equipmentItem->id} ({$equipmentItem->designation}): " . count($childrenItems) . " enfants");
                $children = [];
                $hasMatchingChild = false;
                
                foreach ($childrenItems as $child) {
                    $childNode = $formatForPrimeVue($child);
                    $children[] = $childNode;
                    
                    // Vérifier si cet enfant ou un de ses descendants correspond à la recherche
                    if ($childNode['data']['matches_search'] || 
                        (isset($childNode['styleClass']) && strpos($childNode['styleClass'], 'p-highlight') !== false)) {
                        $hasMatchingChild = true;
                    }
                }
                
                // Ajouter les enfants au noeud
                $node['children'] = $children;
                
                // Log pour débogage
                \Log::info("Ajout de " . count($children) . " enfants au noeud {$equipmentItem->id} ({$equipmentItem->designation})");
                
                // Si un enfant correspond à la recherche, marquer ce noeud comme parent et l'ouvrir
                if ($hasMatchingChild && !empty($search)) {
                    $node['styleClass'] = $node['styleClass'] . ' p-tree-parent-with-match';
                    $node['expanded'] = true;
                }
            }
            
            // Log pour débogage
            if ($directMatch || $isParent || (isset($node['styleClass']) && !empty($node['styleClass']))) {
                \Log::info("Noeud PrimeVue {$equipmentItem->id} ({$equipmentItem->designation}): directMatch={$directMatch}, isParent={$isParent}, styleClass={$node['styleClass']}, expanded={$node['expanded']}");
            }
            
            return $node;
        };
        
        // Convertir tous les équipements au format PrimeVue
        $primeVueNodes = [];
        foreach ($equipment as $item) {
            $node = $formatForPrimeVue($item);
            $primeVueNodes[] = $node;
            
            // Log pour débogage
            \Log::info("Noeud ajouté à l'arbre: {$node['label']} (ID: {$node['key']})");
            if (isset($node['children'])) {
                \Log::info("  - Avec " . count($node['children']) . " enfants");
            } else {
                \Log::info("  - Sans enfants");
            }
        }
        
        // Récupérer tous les équipements pour d'autres usages potentiels
        $allEquipment = Equipment::with('parent')->get()->map(function($item) use ($matchingIds, $parentIds) {
            // Convertir explicitement en booléen
            $directMatch = in_array($item->id, $matchingIds);
            $isParent = in_array($item->id, $parentIds);
            
            $item->matches_search = $directMatch;
            $item->is_parent = $isParent;
            
            // Log pour débogage
            if ($directMatch || $isParent) {
                \Log::info("allEquipment: Équipement {$item->id} ({$item->designation}): matches_search={$item->matches_search}, is_parent={$item->is_parent}, parent_id={$item->parent_id}");
            }
            
            return $item;
        });

        // Log pour débogage final
        \Log::info("Nombre total d'équipements de premier niveau: " . count($equipment));
        \Log::info("Nombre total d'équipements correspondants: " . count(array_filter($allEquipment->toArray(), function($item) {
            return $item['matches_search'] === true;
        })));
        \Log::info("Nombre total de parents: " . count(array_filter($allEquipment->toArray(), function($item) {
            return $item['is_parent'] === true;
        })));
        
        // Log pour débogage de la structure complète
        \Log::info('Structure complète des données PrimeVue: ' . json_encode($primeVueNodes));
        
        // Vérifier si l'utilisateur est admin
        $isAdmin = auth()->user()->hasRole('admin');
        
        // Définir les permissions
        $permissions = [
            'equipment.edit' => $isAdmin || auth()->user()->can('update', Equipment::class),
            'equipment.delete' => $isAdmin || auth()->user()->can('delete', Equipment::class),
            'equipment.create' => $isAdmin || auth()->user()->can('create', Equipment::class),
            'equipment.view' => $isAdmin || auth()->user()->can('view', Equipment::class)
        ];
        
        return Inertia::render('Equipment/Index', [
            'treeData' => $primeVueNodes,
            'allEquipment' => $allEquipment,
            'search' => $search,
            'matchingIds' => $matchingIds,
            'parentIds' => $parentIds,
            'permissions' => $permissions,
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

    /**
     * Trouve récursivement tous les parents des équipements correspondants
     *
     * @param Collection $equipments Les équipements dont on cherche les parents
     * @param array &$parentIds Tableau pour stocker les IDs des parents
     * @return void
     */
    private function findAllParents($equipments, array &$parentIds): void
    {
        foreach ($equipments as $equipment) {
            if ($equipment->parent_id) {
                $parentIds[] = $equipment->parent_id;
                \Log::info("Ajout du parent ID {$equipment->parent_id} pour l'équipement {$equipment->id} ({$equipment->designation})");
                
                // Chercher récursivement les parents du parent
                $parent = Equipment::find($equipment->parent_id);
                if ($parent) {
                    \Log::info("Parent trouvé: {$parent->id} ({$parent->designation})");
                    $this->findAllParents(collect([$parent]), $parentIds);
                } else {
                    \Log::warning("Parent non trouvé pour l'ID {$equipment->parent_id}");
                }
            } else {
                \Log::info("L'équipement {$equipment->id} ({$equipment->designation}) n'a pas de parent");
            }
        }
        
        // Éliminer les doublons
        $parentIds = array_unique($parentIds);
        \Log::info("Parents uniques: " . implode(", ", $parentIds));
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
        
        // Récupérer les documents associés à l'équipement
        $documents = $equipment->documents()->orderBy('created_at', 'desc')->get();
        
        // Vérifier les permissions pour les documents
        $can = [
            'create' => auth()->user()->can('create', \App\Models\EquipmentDocument::class),
            'edit' => auth()->user()->can('update', \App\Models\EquipmentDocument::class),
            'delete' => auth()->user()->can('delete', \App\Models\EquipmentDocument::class)
        ];
        
        return Inertia::render('Equipment/Edit', [
            'equipment' => $equipment,
            'allEquipment' => $equipmentList,
            'documents' => $documents,
            'can' => $can,
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

    /**
     * Affiche les détails d'un équipement
     *
     * @param Equipment $equipment L'équipement à afficher
     * @return Response
     */
    public function show(Equipment $equipment): Response
    {
        // Récupérer les documents associés à l'équipement
        $documents = $equipment->documents()->orderBy('created_at', 'desc')->get();
        
        // Vérifier si l'utilisateur est admin
        $isAdmin = auth()->user()->hasRole('admin');
        
        // Vérifier les permissions pour les documents, en garantissant que les admins ont toutes les permissions
        $can = [
            'edit' => $isAdmin || auth()->user()->can('update', $equipment),
            'delete' => $isAdmin || auth()->user()->can('delete', $equipment),
            'create_document' => $isAdmin || auth()->user()->can('create', \App\Models\EquipmentDocument::class),
            'edit_document' => $isAdmin || auth()->user()->can('update', \App\Models\EquipmentDocument::class),
            'delete_document' => $isAdmin || auth()->user()->can('delete', \App\Models\EquipmentDocument::class)
        ];
        
        // Définir les permissions globales pour la cohérence avec l'index
        $permissions = [
            'equipment.edit' => $isAdmin || auth()->user()->can('update', $equipment),
            'equipment.delete' => $isAdmin || auth()->user()->can('delete', $equipment),
            'equipment.create' => $isAdmin || auth()->user()->can('create', Equipment::class),
            'equipment.view' => $isAdmin || auth()->user()->can('view', Equipment::class)
        ];
        
        return Inertia::render('Equipment/Show', [
            'equipment' => $equipment,
            'documents' => $documents,
            'can' => $can,
            'permissions' => $permissions,
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
        // Vérifier si l'image est un fichier ou une URL existante
        if ($request->hasFile('image')) {
            $imageRule = 'nullable|image|max:2048';
        } else {
            $imageRule = 'nullable|string';
        }

        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'image' => $imageRule,
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
        } elseif (!$request->hasFile('image') && !$request->input('image')) {
            // Si aucune image n'est fournie et que l'image est null dans la requête, supprimer l'image existante
            $validated['image'] = null;
        } elseif (!$request->hasFile('image') && $request->input('image')) {
            // Si l'image est une URL existante, la conserver
            $validated['image'] = $request->input('image');
        }

        $equipment->update($validated);

        return redirect()->route('equipment.show', $equipment->id)
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
