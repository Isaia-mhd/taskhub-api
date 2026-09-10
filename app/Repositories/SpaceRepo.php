<?php

namespace App\Repositories;

use App\Models\Space;
use App\Models\Workspace;
use Illuminate\Support\Facades\Log;

class SpaceRepo
{
    /**
     * Create a new class instance.
     */
    public function spaces(Workspace $workspace): Space
    {
        return $workspace->spaces;
    }

    public function store(Workspace $workspace, array $data): Space
    {
        return $workspace->spaces()->create($data);
    }
}
