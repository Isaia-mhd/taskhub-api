<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('owner_id', 'name', 'description')]
class Workspace extends Model
{
    use HasUuids, HasFactory, HasUniqueSlug;


    public $incrementing = false;
    protected $keyType = 'string';

    public function scopeOwnedBy(Builder $query, string $userId)
    {
        return $query->where('owner_id', $userId);
    }

    public function scopeId(Builder $query, string $workspaceId)
    {
        return $query->where('id', $workspaceId);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'workspace_members')->withPivot('role', 'joined_at');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(WorkspaceInvitation::class);
    }

    protected static function booted()
    {
        static::creating(function ($workspace) {
            $workspace->slug = self::generateSlug($workspace->name);
        });
    }

    public function spaces(): HasMany
    {
        return $this->hasMany(Space::class);
    }
}
