<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTaskListRequest;
use App\Http\Requests\StoreTaskListRequest;
use App\Http\Requests\UpdateTaskListRequest;
use App\Http\Resources\TaskListResource;
use App\Services\TaskListService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class TaskListController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private TaskListService $taskListService) {}

    public function getBySpace(string $space): JsonResponse
    {
        $taskLists = $this->taskListService->getBySpace($space);

        return $this->successJson(
            'Task lists retrieved successfully.',
            TaskListResource::collection($taskLists->load(['space', 'folder']))
        );
    }

    public function getByFolder(string $folder): JsonResponse
    {
        $taskLists = $this->taskListService->getByFolder($folder);

        return $this->successJson(
            'Task lists retrieved successfully.',
            TaskListResource::collection($taskLists->load(['space', 'folder']))
        );
    }

    public function store(StoreTaskListRequest $request): JsonResponse
    {
        $taskList = $this->taskListService->store($request->validated());

        return $this->successJson(
            'Task list stored successfully.',
            new TaskListResource($taskList->load(['space', 'folder'])),
            201
        );
    }

    public function show(string $taskList): JsonResponse
    {
        $taskList = $this->taskListService->getById($taskList);

        return $this->successJson(
            'Task list retrieved successfully.',
            new TaskListResource($taskList->load(['space', 'folder']))
        );
    }

    public function update(UpdateTaskListRequest $request, string $taskList): JsonResponse
    {
        $taskList = $this->taskListService->update($taskList, $request->validated());

        return $this->successJson(
            'Task list updated successfully.',
            new TaskListResource($taskList->load(['space', 'folder']))
        );
    }

    public function destroy(string $taskList): JsonResponse
    {
        $this->taskListService->destroy($taskList);

        return $this->successJson('Task list deleted successfully.');
    }

    public function reorder(ReorderTaskListRequest $request, string $space): JsonResponse
    {
        $this->taskListService->reorder($space, $request->validated());

        return $this->successJson('Task list reordered successfully.');
    }
}
