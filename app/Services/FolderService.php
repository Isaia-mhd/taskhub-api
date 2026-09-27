<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Folder;
use App\Repositories\FolderRepo;
use Illuminate\Database\Eloquent\Collection;

class FolderService
{
    public function __construct(private FolderRepo $folderRepo, private SpaceService $spaceService) {}

    public function getBySpace(string $spaceId): Collection
    {
        $space = $this->spaceService->getById($spaceId);

        return $space->folders;
    }

    public function update(string $folderId, array $data): Folder
    {
        $folder = $this->getById($folderId);

        return $this->folderRepo->update($folder, $data);
    }

    public function destroy(string $folderId): bool
    {
        $folder = $this->getById($folderId);

        return $this->folderRepo->destroy($folder);
    }

    public function getById(string $folderId): Folder
    {
        $folder = $this->folderRepo->getById($folderId);

        if (!$folder) throw new ResourceNotFoundException('Folder not found.');

        return $folder;
    }

    public function store(array $data): Folder
    {
        $space = $this->spaceService->getById($data['space_id']);

        return $this->folderRepo->store($space, $data);
    }

    public function reorder(string $spaceId, array $data): bool
    {
        $space = $this->spaceService->getById($spaceId);

        $this->folderRepo->reorder($space, $data);

        return true;
    }
}
