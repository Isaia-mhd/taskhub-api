<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Exceptions\ResourceNotFoundException;
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

    public function get(User $user, Workspace $workspace): ?Workspace
    {
        $workspace = $this->workspaceRepo->getById($workspace->id);
        
        if(!$workspace) throw new ResourceNotFoundException("Workspace not found.");

        return $this->workspaceRepo->get($user, $workspace->id);
    }

    public function getById(string $workspaceId): Workspace
    {
        $workspace = $this->workspaceRepo->getById($workspaceId);

        if(!$workspace) throw new ResourceNotFoundException("Workspace not found.");

        return $workspace;
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

    public function addMember(Workspace $workspace, User $user): void
    {
        $workspace->members()->syncWithoutDetaching([
            $user->id => [
                'role' => UserRole::MEMBER->value,
                'joined_at' => now()
            ]
        ]);
    }

    public function removeMember(Workspace $workspace, User $user): void
    {
        if (!$workspace->members()->whereKey($user->id)->exists()) {
            abort(404, 'User is not a member of this workspace.');
        }
        $workspace->members()->detach($user->id);
    }

    public function changeMemberRole(Workspace $workspace, User $member, string $userRole): void
    {
        $workspace->members()->updateExistingPivot($member->id, [
            'role' => $userRole
        ]);
    }
}
