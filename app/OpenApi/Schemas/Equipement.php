<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="Equipement",
 *     title="Équipement",
 *     description="Modèle d'équipement",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="designation", type="string", example="Station de travail"),
 *     @OA\Property(property="marque", type="string", example="HP"),
 *     @OA\Property(property="modele", type="string", example="Z4 G4"),
 *     @OA\Property(property="image", type="string", example="workstation.jpg", nullable=true),
 *     @OA\Property(property="icone", type="integer", example=1, nullable=true),
 *     @OA\Property(property="parent_id", type="integer", example=null, nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(
 *         property="all_children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Equipement"),
 *         description="Liste des équipements enfants"
 *     )
 * )
 */
class Equipement {}
