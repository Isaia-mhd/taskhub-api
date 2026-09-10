<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpaceRequest;
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
    public function index()
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
