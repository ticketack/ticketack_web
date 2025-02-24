<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'solver_id',
        'start_at',
        'estimated_duration',
        'comments',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'estimated_duration' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function solver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solver_id');
    }
}
