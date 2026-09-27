<?php

namespace App\Repositories;

use App\Exceptions\ResourceNotFoundException;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskRepo
{
    public function getById(string $taskId): ?Task
    {
        return Task::find($taskId);
    }

    public function getByTaskList(TaskList $taskList): Collection
    {
        return Task::where('list_id', $taskList->id)
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();
    }

    public function store(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function destroy(Task $task): bool
    {
        return $task->delete();
    }

    public function reorder(TaskList $taskList, array $data): void
    {
        DB::transaction(function () use ($taskList, $data) {
            foreach ($data['tasks'] as $item) {
                $task = Task::where('list_id', $taskList->id)
                    ->whereNull('parent_id')
                    ->where('id', $item['id'])
                    ->first();

                if (! $task) {
                    throw new ResourceNotFoundException('Task not found.');
                }

                $task->update([
                    'position' => $item['position'],
                ]);
            }
        });
    }
}
