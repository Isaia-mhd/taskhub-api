<?php

namespace App\Services;

use App\Models\Space;
use App\Models\Workspace;
use App\Repositories\SpaceRepo;

class SpaceService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SpaceRepo $spaceRepo){}


    public function store(array $data): Space
    {
        $workspace = Workspace::findOrFail($data['workspace_id']);

        return $this->spaceRepo->store($workspace, $data);
    }
}
