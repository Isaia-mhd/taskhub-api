<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'position' => $this->position,
            'workspace' => $this->whenLoaded('workspace', [
                'id' => $this->workspace->id,
                'name' => $this->workspace->name
            ]),
            'folders' => FolderResource::collection($this->whenLoaded('folders'))
        ];
    }
}
