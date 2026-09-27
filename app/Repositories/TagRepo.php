<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\Workspace;

class TagRepo
{
    public function getById(int $tagId): ?Tag
    {
        return Tag::find($tagId);
    }

    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);

        return $tag;
    }

    public function destroy(Tag $tag): bool
    {
        return $tag->delete();
    }

    public function store(Workspace $workspace, array $data): Tag
    {
        return $workspace->tags()->create($data);
    }
}
