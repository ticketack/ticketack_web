<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentDocument extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    /**
     * Get the equipment that owns the document.
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Détermine si le fichier est une image.
     *
     * @return bool
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);
    }

    /**
     * Détermine si le fichier est un PDF.
     *
     * @return bool
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    /**
     * Détermine si le fichier est un document Office.
     *
     * @return bool
     */
    public function isOfficeDocument(): bool
    {
        return in_array($this->file_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);
    }

    /**
     * Retourne l'URL du fichier.
     *
     * @return string
     */
    public function getFileUrl(): string
    {
        return $this->file_path;
    }
}
