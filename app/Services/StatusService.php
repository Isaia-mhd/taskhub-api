<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Status;
use App\Repositories\StatusRepo;
use Illuminate\Database\Eloquent\Collection;

class StatusService
{
    public function __construct(private StatusRepo $statusRepo, private WorkspaceService $workspaceService) {}

    public function getByWorkspace(string $workspaceId): Collection
    {
        $workspace = $this->workspaceService->getById($workspaceId);

        return $workspace->statuses;
    }

    public function update(int $statusId, array $data): Status
    {
        $status = $this->getById($statusId);

        return $this->statusRepo->update($status, $data);
    }

    public function destroy(int $statusId): bool
    {
        $status = $this->getById($statusId);

        return $this->statusRepo->destroy($status);
    }

    public function getById(int $statusId): Status
    {
        $status = $this->statusRepo->getById($statusId);

        if (! $status) {
            throw new ResourceNotFoundException('Status not found.');
        }

        return $status;
    }

    public function store(array $data): Status
    {
        $workspace = $this->workspaceService->getById($data['workspace_id']);

        return $this->statusRepo->store($workspace, $data);
    }

    public function reorder(string $workspaceId, array $data): bool
    {
        $workspace = $this->workspaceService->getById($workspaceId);

        $this->statusRepo->reorder($workspace, $data);

        return true;
    }
}
