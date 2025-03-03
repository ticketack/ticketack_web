<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'entry_date',
        'minutes_spent',
        'description',
        'billable',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'minutes_spent' => 'integer',
        'billable' => 'boolean',
    ];

    /**
     * Récupère le ticket associé à cette entrée de temps
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Récupère l'utilisateur associé à cette entrée de temps
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
