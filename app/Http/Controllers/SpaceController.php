<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenericWorkspaceRequest;
use App\Http\Requests\ReorderSpaceRequest;
use App\Http\Requests\StoreSpaceRequest;
use App\Http\Requests\UpdateSpaceRequest;
use App\Http\Resources\SpaceResource;
use App\Http\Resources\WorkspaceResource;
use App\Services\SpaceService;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpaceController extends Controller
{
    use AuthorizesRequests, JsonResponseTrait;
    public function __construct(private SpaceService $spaceService){}
    /**
     * Display a listing of the resource.
     */
    public function getByWorkspace(string $workspace)
    {
        $spaces = $this->spaceService->getByWorkspace($workspace);
         
        return $this->successJson(
            'Spaces retrieved successfully.', 
            SpaceResource::collection($spaces->load(['workspace', 'folders']))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpaceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $data = $this->spaceService->store($validated);
        
        return $this->successJson(
            'Space stored successfully.', 
            new SpaceResource($data->load('workspace')), 
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $space): JsonResponse
    {
        $space = $this->spaceService->getById($space);

        return $this->successJson(
            'Space retrieved successfully.', 
            new SpaceResource($space->load(['workspace', 'folders']))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpaceRequest $request, string $space): JsonResponse
    {
        $validated = $request->validated();

        $updatedSpace = $this->spaceService->update($space, $validated);

        return $this->successJson(
            'Space updated successfully.', 
            new SpaceResource($updatedSpace->load(['workspace', 'folders']))
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $space): JsonResponse
    {
        $this->spaceService->destroy($space);

        return $this->successJson('Space deleted successfully.');
    }

    public function reorder(ReorderSpaceRequest $request, string $workspace): JsonResponse
    {
        $data = $request->validated();

        $this->spaceService->reorder($workspace, $data);

        return $this->successJson('Space reordered successfully.');
    }
}
