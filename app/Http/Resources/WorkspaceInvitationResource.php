<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceInvitationResource extends JsonResource
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
            'workspace_id' => $this->workspace_id,
            'invited_by' => $this->invited_by,
            'user_id' => $this->user_id,
            'email' => $this->email,
            'role' => $this->role,
            'token' => $this->token,
            'status' => $this->status(),
            'expires_at' => $this->expires_at,
            'accepted_at' => $this->accepted_at,
            'rejected_at' => $this->rejected_at,
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'workspace' => $this->whenLoaded('workspace'),
            'inviter' => $this->whenLoaded('inviter'),
        ];
    }

    private function status(): string
    {
        if ($this->accepted_at !== null) return 'accepted';
        if ($this->rejected_at !== null) return 'rejected';
        if ($this->cancelled_at !== null) return 'cancelled';
        if ($this->expires_at !== null && $this->expires_at->isPast()) return 'expired';

        return 'pending';
    }
}
