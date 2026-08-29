<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WorkspaceInvitation>
 */
class WorkspaceInvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'invited_by' => User::factory(),
            'user_id' => User::factory(),
            'email' => fake()->safeEmail(),
            'role' => UserRole::MEMBER->value,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
            'rejected_at' => null,
            'cancelled_at' => null,
        ];
    }
}
