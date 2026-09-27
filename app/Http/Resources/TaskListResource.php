<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskListResource extends JsonResource
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
            'folder' => $this->whenLoaded('folder', function () {
                return $this->folder ? [
                    'id' => $this->folder->id,
                    'name' => $this->folder->name,
                ] : null;
            }),
        ];
    }
}
