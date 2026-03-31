<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'due_date',
        'priority',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Priority order map for sorting (high → medium → low).
     */
    public static array $priorityOrder = [
        'high'   => 1,
        'medium' => 2,
        'low'    => 3,
    ];

    /**
     * Valid status transitions: only forward, no skipping, no reverting.
     */
    public static array $statusFlow = [
        'pending'     => 'in_progress',
        'in_progress' => 'done',
    ];

    /**
     * Check whether a given next status is a valid transition from current status.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        return isset(self::$statusFlow[$this->status])
            && self::$statusFlow[$this->status] === $newStatus;
    }

    /**
     * Scope: filter by status.
     */
    public function scopeOfStatus($query, ?string $status)
    {
        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Scope: sort by priority (high → low) then due_date ascending.
     */
    public function scopeSorted($query)
    {
        return $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                     ->orderBy('due_date', 'asc');
    }
}
