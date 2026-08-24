<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Repositories\WorkspaceRepo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class WorkspaceService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private WorkspaceRepo $workspaceRepo){}

    public function getAll(User $user): LengthAwarePaginator
    {
        return $this->workspaceRepo->getAll($user);
    }

    public function get(User $user, Workspace $workspaceId): Workspace
    {
        return $this->workspaceRepo->get($user, $workspaceId);
    }

    public function store(User $user, array $data): Workspace
    {
        $data['owner_id'] = $user->id;
        return $this->workspaceRepo->store($user, $data);
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        return $this->workspaceRepo->update($workspace, $data);
    }

    public function destroy(Workspace $workspace): bool
    {
        return $this->workspaceRepo->destroy($workspace);
    }
}
