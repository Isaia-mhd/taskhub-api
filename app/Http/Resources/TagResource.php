<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'workspace' => $this->whenLoaded('workspace', fn () => [
                'id' => $this->workspace->id,
                'name' => $this->workspace->name,
            ]),
        ];
    }
}
