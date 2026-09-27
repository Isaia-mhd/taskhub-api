<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeTaskPriorityRequest;
use App\Http\Requests\ChangeTaskStatusRequest;
use App\Http\Requests\MoveTaskRequest;
use App\Http\Requests\ReorderTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskDateRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use JsonResponseTrait;

    public function __construct(private TaskService $taskService) {}

    public function getByTaskList(string $taskList): JsonResponse
    {
        $tasks = $this->taskService->getByTaskList($taskList);

        return $this->successJson(
            'Tasks retrieved successfully.',
            TaskResource::collection($tasks->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->store($request->user(), $request->validated());

        return $this->successJson(
            'Task stored successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator'])),
            201
        );
    }

    public function show(string $task): JsonResponse
    {
        $task = $this->taskService->getById($task);

        return $this->successJson(
            'Task retrieved successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function update(UpdateTaskRequest $request, string $task): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());

        return $this->successJson(
            'Task updated successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function destroy(string $task): JsonResponse
    {
        $this->taskService->destroy($task);

        return $this->successJson('Task deleted successfully.');
    }

    public function move(MoveTaskRequest $request, string $task): JsonResponse
    {
        $task = $this->taskService->move($task, $request->validated()['list_id']);

        return $this->successJson(
            'Task moved successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function changeStatus(ChangeTaskStatusRequest $request, string $task): JsonResponse
    {
        $task = $this->taskService->changeStatus($task, $request->validated()['status_id']);

        return $this->successJson(
            'Task status updated successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function changePriority(ChangeTaskPriorityRequest $request, string $task): JsonResponse
    {
        $task = $this->taskService->changePriority($task, $request->validated()['priority_id']);

        return $this->successJson(
            'Task priority updated successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function updateStartDate(UpdateTaskDateRequest $request, string $task): JsonResponse
    {
        $task = $this->taskService->updateDate($task, 'start_at', $request->validated()['date'] ?? null);

        return $this->successJson(
            'Task start date updated successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function updateDueDate(UpdateTaskDateRequest $request, string $task): JsonResponse
    {
        $task = $this->taskService->updateDate($task, 'due_at', $request->validated()['date'] ?? null);

        return $this->successJson(
            'Task due date updated successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function complete(string $task): JsonResponse
    {
        $task = $this->taskService->complete($task);

        return $this->successJson(
            'Task completed successfully.',
            new TaskResource($task->load(['taskList', 'status', 'priority', 'creator']))
        );
    }

    public function reorder(ReorderTaskRequest $request, string $taskList): JsonResponse
    {
        $this->taskService->reorder($taskList, $request->validated());

        return $this->successJson('Task reordered successfully.');
    }
}
