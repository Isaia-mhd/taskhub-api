<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriorityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'level' => $this->level,
            'color' => $this->color,
            'icon' => $this->icon,
            'workspace' => $this->whenLoaded('workspace', fn () => [
                'id' => $this->workspace->id,
                'name' => $this->workspace->name,
            ]),
        ];
    }
}
