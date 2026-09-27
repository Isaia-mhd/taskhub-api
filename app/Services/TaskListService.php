<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Folder;
use App\Models\TaskList;
use App\Repositories\TaskListRepo;
use Illuminate\Database\Eloquent\Collection;

class TaskListService
{
    public function __construct(private TaskListRepo $taskListRepo, private SpaceService $spaceService, private FolderService $folderService) {}

    public function getBySpace(string $spaceId): Collection
    {
        $space = $this->spaceService->getById($spaceId);

        return $space->taskLists;
    }

    public function getByFolder(string $folderId): Collection
    {
        $folder = $this->folderService->getById($folderId);

        return $folder->taskLists;
    }

    public function update(string $taskListId, array $data): TaskList
    {
        $taskList = $this->getById($taskListId);

        if (array_key_exists('folder_id', $data) && $data['folder_id']) {
            $folder = $this->folderService->getById($data['folder_id']);
            $this->ensureFolderBelongsToSpace($folder, $taskList->space_id);
        }

        return $this->taskListRepo->update($taskList, $data);
    }

    public function destroy(string $taskListId): bool
    {
        $taskList = $this->getById($taskListId);

        return $this->taskListRepo->destroy($taskList);
    }

    public function getById(string $taskListId): TaskList
    {
        $taskList = $this->taskListRepo->getById($taskListId);

        if (! $taskList) {
            throw new ResourceNotFoundException('Task list not found.');
        }

        return $taskList;
    }

    public function store(array $data): TaskList
    {
        $space = $this->spaceService->getById($data['space_id']);

        if (! empty($data['folder_id'])) {
            $folder = $this->folderService->getById($data['folder_id']);
            $this->ensureFolderBelongsToSpace($folder, $space->id);
        }

        return $this->taskListRepo->store($space, $data);
    }

    public function reorder(string $spaceId, array $data): bool
    {
        $space = $this->spaceService->getById($spaceId);

        $this->taskListRepo->reorder($space, $data);

        return true;
    }

    private function ensureFolderBelongsToSpace(Folder $folder, string $spaceId): void
    {
        if ($folder->space_id !== $spaceId) {
            throw new ResourceNotFoundException('Folder not found.');
        }
    }
}
