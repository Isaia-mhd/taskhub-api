<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
}
