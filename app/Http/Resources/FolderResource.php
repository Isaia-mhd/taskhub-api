<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'position' => $this->position,
            'space' => $this->whenLoaded('space', fn () => [
                'id' => $this->space->id,
                'name' => $this->space->name,
            ]),
            'task_lists' => TaskListResource::collection($this->whenLoaded('taskLists')),
        ];
    }
}
