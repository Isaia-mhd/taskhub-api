<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasUuids;

    protected $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function taskLists(): HasMany
    {
        return $this->hasMany(TaskList::class);
    }
}
