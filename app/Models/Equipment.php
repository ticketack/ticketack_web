<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'designation',
        'marque',
        'modele',
        'image',
        'icone',
        'parent_id',
        'serial_number'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Equipment::class, 'parent_id');
    }

    /**
     * Récupère tous les enfants de l'équipement de manière récursive
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(Equipment::class, 'parent_id')->with('allChildren');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'equipment_id');
    }

    /**
     * Get the documents for the equipment.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EquipmentDocument::class, 'equipment_id');
    }
}
