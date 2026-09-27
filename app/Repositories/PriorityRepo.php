<?php

namespace App\Repositories;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Priority;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class PriorityRepo
{
    public function getById(int $priorityId): ?Priority
    {
        return Priority::find($priorityId);
    }

    public function update(Priority $priority, array $data): Priority
    {
        $priority->update($data);

        return $priority;
    }

    public function destroy(Priority $priority): bool
    {
        return $priority->delete();
    }

    public function store(Workspace $workspace, array $data): Priority
    {
        return $workspace->priorities()->create($data);
    }

    public function reorder(Workspace $workspace, array $data): void
    {
        DB::transaction(function () use ($workspace, $data) {
            foreach ($data['priorities'] as $item) {
                $priority = Priority::where('workspace_id', $workspace->id)
                    ->where('id', $item['id'])
                    ->first();

                if (! $priority) {
                    throw new ResourceNotFoundException('Priority not found.');
                }

                $priority->update([
                    'level' => $item['level'],
                ]);
            }
        });
    }
}
