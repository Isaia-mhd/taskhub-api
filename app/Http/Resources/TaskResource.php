<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workspace_id' => $this->workspace_id,
            'list_id' => $this->list_id,
            'parent_id' => $this->parent_id,
            'status_id' => $this->status_id,
            'creator_id' => $this->creator_id,
            'priority_id' => $this->priority_id,
            'title' => $this->title,
            'description' => $this->description,
            'start_at' => $this->start_at,
            'due_at' => $this->due_at,
            'completed_at' => $this->completed_at,
            'position' => $this->position,
            'estimate_minutes' => $this->estimate_minutes,
            'task_list' => $this->whenLoaded('taskList', fn () => [
                'id' => $this->taskList->id,
                'name' => $this->taskList->name,
            ]),
            'status' => $this->whenLoaded('status', fn () => [
                'id' => $this->status->id,
                'name' => $this->status->name,
                'type' => $this->status->type,
            ]),
            'priority' => $this->whenLoaded('priority', fn () => [
                'id' => $this->priority->id,
                'name' => $this->priority->name,
                'level' => $this->priority->level,
            ]),
            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
        ];
    }
}
