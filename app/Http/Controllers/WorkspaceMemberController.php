<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddWorkspaceMemberRequest;
use App\Http\Requests\ChangeWorkspaceMemberRoleRequest;
use App\Http\Requests\RemoveWorkspaceMemberRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkspaceMemberController extends Controller
{
    use JsonResponseTrait, AuthorizesRequests;

    public function __construct(private WorkspaceService $workspaceService){}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function members(Workspace $workspace): JsonResponse {
        $members = $workspace->members()->paginate(10);

        return $this->successJson(
            'Workspace members loaded.',
            UserResource::collection($members)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddWorkspaceMemberRequest $request): JsonResponse
    {
        $workspace = Workspace::findOrFail($request->validated('workspace_id'));

        $this->authorize('addMember', $workspace);

        $user = User::findOrFail($request->validated('user_id'));

        $this->workspaceService->addMember($workspace, $user);

        return $this->successJson('Member added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RemoveWorkspaceMemberRequest $request, Workspace $workspace_member)
    {

        $this->authorize('removeMember', $workspace_member);

        $user = User::findOrFail($request->validated('user_id'));

        $this->workspaceService->removeMember($workspace_member, $user);

        return $this->successJson('Member removed successfully.');
    }

    public function changeMemberRole(ChangeWorkspaceMemberRoleRequest $request, Workspace $workspace, User $member): JsonResponse
    {
        $this->authorize('changeMemberRole', $workspace);

       $this->workspaceService->changeMemberRole($workspace, $member, $request->validated(['role']));

        return $this->successJson('Role changed successfully');
    }
}
