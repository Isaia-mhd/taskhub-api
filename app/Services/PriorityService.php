<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Priority;
use App\Repositories\PriorityRepo;
use Illuminate\Database\Eloquent\Collection;

class PriorityService
{
    public function __construct(private PriorityRepo $priorityRepo, private WorkspaceService $workspaceService) {}

    public function getByWorkspace(string $workspaceId): Collection
    {
        $workspace = $this->workspaceService->getById($workspaceId);

        return $workspace->priorities;
    }

    public function update(int $priorityId, array $data): Priority
    {
        $priority = $this->getById($priorityId);

        return $this->priorityRepo->update($priority, $data);
    }

    public function destroy(int $priorityId): bool
    {
        $priority = $this->getById($priorityId);

        return $this->priorityRepo->destroy($priority);
    }

    public function getById(int $priorityId): Priority
    {
        $priority = $this->priorityRepo->getById($priorityId);

        if (! $priority) {
            throw new ResourceNotFoundException('Priority not found.');
        }

        return $priority;
    }

    public function store(array $data): Priority
    {
        $workspace = $this->workspaceService->getById($data['workspace_id']);

        return $this->priorityRepo->store($workspace, $data);
    }

    public function reorder(string $workspaceId, array $data): bool
    {
        $workspace = $this->workspaceService->getById($workspaceId);

        $this->priorityRepo->reorder($workspace, $data);

        return true;
    }
}
