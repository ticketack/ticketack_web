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

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'equipment_id');
    }
}
