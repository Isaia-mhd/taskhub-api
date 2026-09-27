<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderStatusRequest;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusResource;
use App\Services\StatusService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private StatusService $statusService) {}

    public function getByWorkspace(string $workspace): JsonResponse
    {
        $statuses = $this->statusService->getByWorkspace($workspace);

        return $this->successJson(
            'Statuses retrieved successfully.',
            StatusResource::collection($statuses->load('workspace'))
        );
    }

    public function store(StoreStatusRequest $request): JsonResponse
    {
        $status = $this->statusService->store($request->validated());

        return $this->successJson(
            'Status stored successfully.',
            new StatusResource($status->load('workspace')),
            201
        );
    }

    public function show(int $status): JsonResponse
    {
        $status = $this->statusService->getById($status);

        return $this->successJson(
            'Status retrieved successfully.',
            new StatusResource($status->load('workspace'))
        );
    }

    public function update(UpdateStatusRequest $request, int $status): JsonResponse
    {
        $status = $this->statusService->update($status, $request->validated());

        return $this->successJson(
            'Status updated successfully.',
            new StatusResource($status->load('workspace'))
        );
    }

    public function destroy(int $status): JsonResponse
    {
        $this->statusService->destroy($status);

        return $this->successJson('Status deleted successfully.');
    }

    public function reorder(ReorderStatusRequest $request, string $workspace): JsonResponse
    {
        $this->statusService->reorder($workspace, $request->validated());

        return $this->successJson('Status reordered successfully.');
    }
}
