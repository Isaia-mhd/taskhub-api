<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderPriorityRequest;
use App\Http\Requests\StorePriorityRequest;
use App\Http\Requests\UpdatePriorityRequest;
use App\Http\Resources\PriorityResource;
use App\Services\PriorityService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class PriorityController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private PriorityService $priorityService) {}

    public function getByWorkspace(string $workspace): JsonResponse
    {
        $priorities = $this->priorityService->getByWorkspace($workspace);

        return $this->successJson(
            'Priorities retrieved successfully.',
            PriorityResource::collection($priorities->load('workspace'))
        );
    }

    public function store(StorePriorityRequest $request): JsonResponse
    {
        $priority = $this->priorityService->store($request->validated());

        return $this->successJson(
            'Priority stored successfully.',
            new PriorityResource($priority->load('workspace')),
            201
        );
    }

    public function show(int $priority): JsonResponse
    {
        $priority = $this->priorityService->getById($priority);

        return $this->successJson(
            'Priority retrieved successfully.',
            new PriorityResource($priority->load('workspace'))
        );
    }

    public function update(UpdatePriorityRequest $request, int $priority): JsonResponse
    {
        $priority = $this->priorityService->update($priority, $request->validated());

        return $this->successJson(
            'Priority updated successfully.',
            new PriorityResource($priority->load('workspace'))
        );
    }

    public function destroy(int $priority): JsonResponse
    {
        $this->priorityService->destroy($priority);

        return $this->successJson('Priority deleted successfully.');
    }

    public function reorder(ReorderPriorityRequest $request, string $workspace): JsonResponse
    {
        $this->priorityService->reorder($workspace, $request->validated());

        return $this->successJson('Priority reordered successfully.');
    }
}
