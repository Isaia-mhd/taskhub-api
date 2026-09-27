<?php

namespace App\Services;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Tag;
use App\Repositories\TagRepo;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    public function __construct(private TagRepo $tagRepo, private WorkspaceService $workspaceService) {}

    public function getByWorkspace(string $workspaceId): Collection
    {
        $workspace = $this->workspaceService->getById($workspaceId);

        return $workspace->tags;
    }

    public function getById(int $tagId): Tag
    {
        $tag = $this->tagRepo->getById($tagId);

        if (! $tag) {
            throw new ResourceNotFoundException('Tag not found.');
        }

        return $tag;
    }

    public function store(array $data): Tag
    {
        $workspace = $this->workspaceService->getById($data['workspace_id']);

        return $this->tagRepo->store($workspace, $data);
    }

    public function update(int $tagId, array $data): Tag
    {
        return $this->tagRepo->update($this->getById($tagId), $data);
    }

    public function destroy(int $tagId): bool
    {
        return $this->tagRepo->destroy($this->getById($tagId));
    }
}
