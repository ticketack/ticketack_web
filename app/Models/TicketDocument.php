<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class TicketDocument extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
        'size'
    ];

    protected $appends = ['url'];

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_document')
            ->withTimestamps();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->path);
    }

    public function delete()
    {
        // Supprimer le fichier du bucket S3
        Storage::disk('s3')->delete($this->path);
        
        // Supprimer l'enregistrement de la base de données
        return parent::delete();
    }
}
