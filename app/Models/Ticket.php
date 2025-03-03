<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'category_id',
        'status_id',
        'archived',
        'is_public',
        'equipment_id',
        'created_by',
        'due_date',
        'planned_start',
        'planned_end',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_public' => 'boolean',
        'archived' => 'boolean',
        'planned_start' => 'datetime',
        'planned_end' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_assignees')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class)->with('user')->latest();
    }

    public function addLog(string $type, string $message, array $properties = []): void
    {
        $this->logs()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'message' => $message,
            'properties' => $properties
        ]);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(TicketDocument::class, 'ticket_document')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->with('user')->latest();
    }
    
    /**
     * Récupère les entrées de temps associées à ce ticket
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class)->with('user')->latest();
    }
    
    /**
     * Calcule le temps total passé sur ce ticket en minutes
     */
    public function getTotalTimeSpentAttribute(): int
    {
        return $this->timeEntries()->sum('minutes_spent');
    }
}
