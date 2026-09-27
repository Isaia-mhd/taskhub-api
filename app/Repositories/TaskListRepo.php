<?php

namespace App\Repositories;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Space;
use App\Models\TaskList;
use Illuminate\Support\Facades\DB;

class TaskListRepo
{
    public function getById(string $taskListId): ?TaskList
    {
        return TaskList::find($taskListId);
    }

    public function update(TaskList $taskList, array $data): TaskList
    {
        $taskList->update($data);

        return $taskList;
    }

    public function destroy(TaskList $taskList): bool
    {
        return $taskList->delete();
    }

    public function store(Space $space, array $data): TaskList
    {
        return $space->taskLists()->create($data);
    }

    public function reorder(Space $space, array $data): void
    {
        DB::transaction(function () use ($space, $data) {
            foreach ($data['task_lists'] as $item) {
                $taskList = TaskList::where('space_id', $space->id)
                    ->where('id', $item['id'])
                    ->first();

                if (! $taskList) {
                    throw new ResourceNotFoundException('Task list not found.');
                }

                $taskList->update([
                    'position' => $item['position'],
                ]);
            }
        });
    }
}
