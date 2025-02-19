<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Équipements",
 *     description="API Endpoints pour la gestion des équipements"
 * )
 */
class EquipementController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        return Equipement::where('designation', 'like', "%{$query}%")
            ->orWhere('marque', 'like', "%{$query}%")
            ->orWhere('modele', 'like', "%{$query}%")
            ->take(10)
            ->get();
    }
    /**
     * @OA\Get(
     *     path="/api/equipements",
     *     summary="Liste tous les équipements",
     *     description="Retourne la liste de tous les équipements avec leur hiérarchie",
     *     operationId="indexEquipement",
     *     tags={"Équipements"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des équipements récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Equipement"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $equipements = Equipement::with('allChildren')
            ->whereNull('parent_id')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $equipements
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/equipements",
     *     summary="Crée un nouvel équipement",
     *     description="Crée un nouvel équipement avec les données fournies",
     *     operationId="storeEquipement",
     *     tags={"Équipements"},
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"designation", "marque", "modele"},
     *             @OA\Property(property="designation", type="string", example="Station de travail"),
     *             @OA\Property(property="marque", type="string", example="HP"),
     *             @OA\Property(property="modele", type="string", example="Z4 G4"),
     *             @OA\Property(property="image", type="string", example="workstation.jpg"),
     *             @OA\Property(property="icone", type="integer", example=1),
     *             @OA\Property(property="parent_id", type="integer", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Équipement créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Équipement créé avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/Equipement")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'icone' => 'nullable|integer',
            'parent_id' => 'nullable|exists:equipements,id'
        ]);

        $equipement = Equipement::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Équipement créé avec succès',
            'data' => $equipement
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/equipements/{equipement}",
     *     summary="Affiche les détails d'un équipement",
     *     description="Retourne les détails d'un équipement spécifique avec ou sans sa hiérarchie",
     *     operationId="showEquipement",
     *     tags={"Équipements"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="equipement",
     *         description="ID de l'équipement",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="children",
     *         description="Inclure les équipements enfants (yes/no)",
     *         required=false,
     *         in="query",
     *         @OA\Schema(type="string", enum={"yes", "no"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'équipement récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/Equipement")
     *         )
     *     )
     * )
     */
    public function show(string $id, Request $request): JsonResponse
    {
        $query = Equipement::query();
        
        if ($request->query('children') === 'yes') {
            $query->with('allChildren');
        }
        
        $equipement = $query->where('id', $id)->first();
        
        if (!$equipement) {
            return response()->json([
                'status' => 'error',
                'message' => 'Équipement non trouvé'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $equipement
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/equipements/{equipement}",
     *     summary="Met à jour un équipement",
     *     description="Met à jour les informations d'un équipement existant",
     *     operationId="updateEquipement",
     *     tags={"Équipements"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="equipement",
     *         description="ID de l'équipement",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"designation", "marque", "modele"},
     *             @OA\Property(property="designation", type="string", example="Station de travail principale"),
     *             @OA\Property(property="marque", type="string", example="HP"),
     *             @OA\Property(property="modele", type="string", example="Z4 G4"),
     *             @OA\Property(property="image", type="string", example="workstation.jpg"),
     *             @OA\Property(property="icone", type="integer", example=1),
     *             @OA\Property(property="parent_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Équipement mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Équipement mis à jour avec succès"),
     *             @OA\Property(property="data", ref="#/components/schemas/Equipement")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Equipement $equipement): JsonResponse
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'icone' => 'nullable|integer',
            'parent_id' => 'nullable|exists:equipements,id'
        ]);

        $equipement->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Équipement mis à jour avec succès',
            'data' => $equipement
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/equipements/{equipement}",
     *     summary="Supprime un équipement",
     *     description="Supprime un équipement existant",
     *     operationId="destroyEquipement",
     *     tags={"Équipements"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="equipement",
     *         description="ID de l'équipement",
     *         required=true,
     *         in="path",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Équipement supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Équipement supprimé avec succès")
     *         )
     *     )
     * )
     */
    public function destroy(Equipement $equipement): JsonResponse
    {
        $equipement->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Équipement supprimé avec succès'
        ]);
    }
}
