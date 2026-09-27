<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepo;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private TaskRepo $taskRepo,
        private TaskListService $taskListService,
        private StatusService $statusService,
        private PriorityService $priorityService,
        private TagService $tagService,
        private WorkspaceService $workspaceService
    ) {}

    public function getByTaskList(string $taskListId): Collection
    {
        $taskList = $this->taskListService->getById($taskListId);

        return $this->taskRepo->getByTaskList($taskList);
    }

    public function getSubtasks(string $taskId): Collection
    {
        return $this->taskRepo->getSubtasks($this->getById($taskId));
    }

    public function getSubtask(string $taskId, string $subtaskId): Task
    {
        $subtask = $this->getById($subtaskId);

        if ($subtask->parent_id !== $taskId) {
            throw new ResourceNotFoundException('Subtask not found.');
        }

        return $subtask;
    }

    public function storeSubtask(User $user, string $taskId, array $data): Task
    {
        $parent = $this->getById($taskId);

        $data['parent_id'] = $parent->id;
        $data['list_id'] = $parent->list_id;
        $data['workspace_id'] = $parent->workspace_id;
        $data['creator_id'] = $user->id;
        $data['status_id'] ??= $parent->status_id;
        $data['priority_id'] ??= $parent->priority_id;

        $this->ensureStatusAndPriorityBelongToWorkspace($data['status_id'], $data['priority_id'], $parent->workspace_id);

        return $this->taskRepo->store($data);
    }

    public function updateSubtask(string $taskId, string $subtaskId, array $data): Task
    {
        return $this->taskRepo->update($this->getSubtask($taskId, $subtaskId), $data);
    }

    public function destroySubtask(string $taskId, string $subtaskId): bool
    {
        return $this->taskRepo->destroy($this->getSubtask($taskId, $subtaskId));
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

    public function getAssignees(string $taskId): Collection
    {
        return $this->taskRepo->getAssignees($this->getById($taskId));
    }

    public function assignUser(string $taskId, string $userId): void
    {
        $this->assignUsers($taskId, [$userId]);
    }

    public function assignUsers(string $taskId, array $userIds): void
    {
        $task = $this->getById($taskId);

        foreach ($userIds as $userId) {
            $this->getWorkspaceUser($task, $userId);
        }

        $this->taskRepo->assignUsers($task, $userIds);
    }

    public function removeAssignee(string $taskId, string $userId): void
    {
        $task = $this->getById($taskId);
        $user = $this->getWorkspaceUser($task, $userId);

        if (! $this->taskRepo->removeAssignee($task, $user)) {
            throw new ResourceNotFoundException('Assignee not found.');
        }
    }

    public function getTags(string $taskId): Collection
    {
        return $this->taskRepo->getTags($this->getById($taskId));
    }

    public function attachTag(string $taskId, int $tagId): void
    {
        $this->attachTags($taskId, [$tagId]);
    }

    public function attachTags(string $taskId, array $tagIds): void
    {
        $task = $this->getById($taskId);

        foreach ($tagIds as $tagId) {
            $tag = $this->tagService->getById($tagId);
            $this->ensureTagBelongsToWorkspace($tag, $task->workspace_id);
        }

        $this->taskRepo->attachTags($task, $tagIds);
    }

    public function removeTag(string $taskId, int $tagId): void
    {
        $task = $this->getById($taskId);
        $tag = $this->tagService->getById($tagId);
        $this->ensureTagBelongsToWorkspace($tag, $task->workspace_id);

        if (! $this->taskRepo->removeTag($task, $tag->id)) {
            throw new ResourceNotFoundException('Task tag not found.');
        }
    }

    public function reorder(string $taskListId, array $data): bool
    {
        $taskList = $this->taskListService->getById($taskListId);

        $this->taskRepo->reorder($taskList, $data);

        return true;
    }

    private function getWorkspaceUser(Task $task, string $userId): User
    {
        $user = User::find($userId);
        $workspace = $this->workspaceService->getById($task->workspace_id);

        if (! $user || ($workspace->owner_id !== $user->id && ! $workspace->members()->whereKey($user->id)->exists())) {
            throw new ResourceNotFoundException('User not found.');
        }

        return $user;
    }

    private function ensureTagBelongsToWorkspace(Tag $tag, string $workspaceId): void
    {
        if ($tag->workspace_id !== $workspaceId) {
            throw new ResourceNotFoundException('Tag not found.');
        }
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
