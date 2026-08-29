<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceInvitation extends Model
{
    /** @use HasFactory<\Database\Factories\WorkspaceInvitationFactory> */
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';


    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'invited_by'
        );
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null
            && $this->rejected_at === null
            && $this->cancelled_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
