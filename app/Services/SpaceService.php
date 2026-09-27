<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Space;
use App\Models\Workspace;
use App\Repositories\SpaceRepo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SpaceService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SpaceRepo $spaceRepo, private WorkspaceService $workspaceService){}

    public function getByWorkspace( string $workspaceId): Collection
    {
        $workspace = $this->workspaceService->getById($workspaceId);

        return $workspace->spaces;
    }

    public function update(string $space, array $data): Space
    {
        $space = $this->getById($space);
        return $this->spaceRepo->update($space, $data);
    }

    public function destroy(string $space): bool
    {
        $space = $this->getById($space);
        return $this->spaceRepo->destroy($space);
    }

    public function getById(string $spaceId): Space
    {
        $space = $this->spaceRepo->getById($spaceId);

        if(!$space) throw new ResourceNotFoundException("Space not found.");

        return $space;
    }

    public function store(array $data): Space
    {
        $workspace = $this->workspaceService->getById($data['workspace_id']);

        return $this->spaceRepo->store($workspace, $data);
    }

    public function reorder(string $workspace, array $data): bool
    {
        $workspace = $this->workspaceService->getById($workspace);

        $this->spaceRepo->reorder($workspace, $data);
        
        return true;
    }
}
