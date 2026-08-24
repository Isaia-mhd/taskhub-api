<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddWorkspaceMemberRequest;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    use JsonResponseTrait, AuthorizesRequests;
    public function __construct(private WorkspaceService $workspaceService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->successJson(
            'Workspaces loaded.', 
            WorkspaceResource::collection($this->workspaceService->getAll($request->user()))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->successJson(
            'Workspace created successfully.', 
            new WorkspaceResource($this->workspaceService->store($request->user(), $data)), 
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Workspace $workspace): JsonResponse
    {
        return $this->successJson('A workspace loaded.', new WorkspaceResource($this->workspaceService->get($request->user(), $workspace)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Workspace $workspace): JsonResponse
    {
        $data = $request->validated();

        return $this->successJson(
            'Workspace updated succesfully.',
            new WorkspaceResource($this->workspaceService->update($workspace, $data))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->workspaceService->destroy($workspace);

        return $this->successJson('Workspace deleted successfully.');
    }

    public function addMember(AddWorkspaceMemberRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('addMember', $workspace);

        $user = User::findOrFail($request->validated('user_id'));

        $this->workspaceService->addMember($workspace, $user);

        return $this->successJson('Member added successfully.');
    }
}
