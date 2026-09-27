<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkspaceRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAll(User $user): LengthAwarePaginator
    {
        return Workspace::ownedBy($user->id)->paginate(10);
    }

    public function get(User $user, string $workspace): ?Workspace
    {
        return Workspace::ownedBy($user->id)->id($workspace)->first();
    }

    public function getById(string $workspaceId): ?Workspace
    {
        return Workspace::find($workspaceId);
    }

    public function store(User $user, array $data): Workspace
    {
        return $user->workspaces()->create($data);
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        $workspace->update($data);

        return $workspace;
    }

    public function destroy(Workspace $workspace): bool
    {
        return $workspace->delete();
    }
}
