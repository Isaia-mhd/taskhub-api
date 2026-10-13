<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'role' => $this->whenPivotLoaded('workspace_members', 'role'),
            'joined_at' => $this->whenPivotLoaded('workspace_members', 'joined_at'),
            'workspaces' => WorkspaceResource::collection($this->whenLoaded('workspaces'))
        ];
    }
}
