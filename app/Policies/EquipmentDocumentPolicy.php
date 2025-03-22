<?php

namespace App\Policies;

use App\Models\EquipmentDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EquipmentDocumentPolicy
{
    /**
     * Détermine si l'utilisateur peut effectuer des actions avant la vérification des autres permissions.
     */
    public function before(User $user, $ability): ?bool
    {
        // Les administrateurs ont toutes les permissions
        if ($user->hasRole('admin')) {
            return true;
        }
        
        return null; // Null signifie continuer avec les vérifications normales
    }
    
    /**
     * Détermine si l'utilisateur peut voir la liste des documents.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('equipment.view');
    }

    /**
     * Détermine si l'utilisateur peut voir un document spécifique.
     */
    public function view(User $user, EquipmentDocument $equipmentDocument): bool
    {
        return $user->hasPermissionTo('equipment.view');
    }

    /**
     * Détermine si l'utilisateur peut créer des documents.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('equipment.create');
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un document.
     */
    public function update(User $user, EquipmentDocument $equipmentDocument): bool
    {
        return $user->hasPermissionTo('equipment.edit');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un document.
     */
    public function delete(User $user, EquipmentDocument $equipmentDocument): bool
    {
        return $user->hasPermissionTo('equipment.delete');
    }

    /**
     * Détermine si l'utilisateur peut restaurer un document supprimé.
     */
    public function restore(User $user, EquipmentDocument $equipmentDocument): bool
    {
        return $user->hasPermissionTo('equipment.edit');
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement un document.
     */
    public function forceDelete(User $user, EquipmentDocument $equipmentDocument): bool
    {
        return $user->hasPermissionTo('equipment.delete');
    }
}
