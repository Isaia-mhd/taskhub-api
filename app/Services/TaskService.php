<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Repositories\TaskRepo;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private TaskRepo $taskRepo,
        private TaskListService $taskListService,
        private StatusService $statusService,
        private PriorityService $priorityService
    ) {}

    public function getByTaskList(string $taskListId): Collection
    {
        $taskList = $this->taskListService->getById($taskListId);

        return $this->taskRepo->getByTaskList($taskList);
    }

    public function getById(string $taskId): Task
    {
        $task = $this->taskRepo->getById($taskId);

        if (! $task) {
            throw new ResourceNotFoundException('Task not found.');
        }

        return $task;
    }

    public function store(User $user, array $data): Task
    {
        $taskList = $this->taskListService->getById($data['list_id']);
        $workspaceId = $taskList->space->workspace_id;

        $this->ensureStatusAndPriorityBelongToWorkspace($data['status_id'], $data['priority_id'], $workspaceId);

        $data['workspace_id'] = $workspaceId;
        $data['creator_id'] = $user->id;

        return $this->taskRepo->store($data);
    }

    public function update(string $taskId, array $data): Task
    {
        return $this->taskRepo->update($this->getById($taskId), $data);
    }

    public function destroy(string $taskId): bool
    {
        return $this->taskRepo->destroy($this->getById($taskId));
    }

    public function move(string $taskId, string $taskListId): Task
    {
        $task = $this->getById($taskId);
        $taskList = $this->taskListService->getById($taskListId);

        if ($taskList->space->workspace_id !== $task->workspace_id) {
            throw new ResourceNotFoundException('Task list not found.');
        }

        return $this->taskRepo->update($task, ['list_id' => $taskList->id]);
    }

    public function changeStatus(string $taskId, int $statusId): Task
    {
        $task = $this->getById($taskId);
        $status = $this->statusService->getById($statusId);

        $this->ensureBelongsToWorkspace($status, $task->workspace_id, 'Status');

        return $this->taskRepo->update($task, ['status_id' => $status->id]);
    }

    public function changePriority(string $taskId, int $priorityId): Task
    {
        $task = $this->getById($taskId);
        $priority = $this->priorityService->getById($priorityId);

        $this->ensureBelongsToWorkspace($priority, $task->workspace_id, 'Priority');

        return $this->taskRepo->update($task, ['priority_id' => $priority->id]);
    }

    public function updateDate(string $taskId, string $field, ?string $date): Task
    {
        return $this->taskRepo->update($this->getById($taskId), [$field => $date]);
    }

    public function complete(string $taskId): Task
    {
        return $this->taskRepo->update($this->getById($taskId), ['completed_at' => now()]);
    }

    public function reorder(string $taskListId, array $data): bool
    {
        $taskList = $this->taskListService->getById($taskListId);

        $this->taskRepo->reorder($taskList, $data);

        return true;
    }

    private function ensureStatusAndPriorityBelongToWorkspace(int $statusId, int $priorityId, string $workspaceId): void
    {
        $this->ensureBelongsToWorkspace($this->statusService->getById($statusId), $workspaceId, 'Status');
        $this->ensureBelongsToWorkspace($this->priorityService->getById($priorityId), $workspaceId, 'Priority');
    }

    private function ensureBelongsToWorkspace(Status|Priority $resource, string $workspaceId, string $resourceName): void
    {
        if ($resource->workspace_id !== $workspaceId) {
            throw new ResourceNotFoundException($resourceName.' not found.');
        }
    }
}
