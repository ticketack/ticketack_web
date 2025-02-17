<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipement extends Model
{
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
        return $this->belongsTo(Equipement::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Equipement::class, 'parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }
}
