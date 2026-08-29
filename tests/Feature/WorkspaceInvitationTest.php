<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\WorkspaceInvitationMail;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_send_workspace_invitation(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        Sanctum::actingAs($owner);

        $response = $this->postJson("/api/v1/workspaces/{$workspace->id}/invitations", [
            'email' => 'invited@example.com',
            'role' => UserRole::MEMBER->value,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.email', 'invited@example.com');
        $response->assertJsonPath('data.status', 'pending');
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'token',
                'email',
                'role',
                'status',
                'expires_at',
            ],
        ]);

        $this->assertDatabaseHas('workspace_invitations', [
            'workspace_id' => $workspace->id,
            'invited_by' => $owner->id,
            'email' => 'invited@example.com',
            'role' => UserRole::MEMBER->value,
        ]);

        Mail::assertSent(
            WorkspaceInvitationMail::class,
            fn (WorkspaceInvitationMail $mail) => $mail->hasTo('invited@example.com')
        );
    }

    public function test_owner_can_list_pending_workspace_invitations(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'invited_by' => $owner->id,
            'email' => 'pending@example.com',
        ]);

        WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'invited_by' => $owner->id,
            'email' => 'accepted@example.com',
            'accepted_at' => now(),
        ]);

        Sanctum::actingAs($owner);

        $response = $this->getJson("/api/v1/workspaces/{$workspace->id}/invitations");

        $response->assertOk();
        $response->assertJsonFragment([
            'email' => 'pending@example.com',
            'status' => 'pending',
        ]);
        $response->assertJsonMissing([
            'email' => 'accepted@example.com',
        ]);
    }

    public function test_invitation_can_be_shown_by_token(): void
    {
        $invitation = WorkspaceInvitation::factory()->create([
            'email' => 'pending@example.com',
        ]);

        $response = $this->getJson("/api/v1/invitations/{$invitation->token}");

        $response->assertOk();
        $response->assertJsonPath('data.email', 'pending@example.com');
        $response->assertJsonPath('data.token', $invitation->token);
    }

    public function test_recipient_can_accept_invitation(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create([
            'email' => 'recipient@example.com',
        ]);
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $invitation = WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'invited_by' => $owner->id,
            'user_id' => null,
            'email' => $recipient->email,
            'role' => UserRole::ADMIN->value,
        ]);

        Sanctum::actingAs($recipient);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/accept");

        $response->assertOk();
        $response->assertJsonPath('message', 'Invitation accepted successfully.');

        $this->assertDatabaseHas('workspace_members', [
            'workspace_id' => $workspace->id,
            'user_id' => $recipient->id,
            'role' => UserRole::ADMIN->value,
        ]);
        $this->assertDatabaseHas('workspace_invitations', [
            'id' => $invitation->id,
            'user_id' => $recipient->id,
        ]);
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    public function test_another_user_cannot_accept_invitation(): void
    {
        $recipient = User::factory()->create([
            'email' => 'recipient@example.com',
        ]);
        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
        ]);
        $invitation = WorkspaceInvitation::factory()->create([
            'user_id' => $recipient->id,
            'email' => $recipient->email,
        ]);

        Sanctum::actingAs($otherUser);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/accept");

        $response->assertForbidden();
        $this->assertNull($invitation->fresh()->accepted_at);
    }

    public function test_recipient_can_reject_invitation(): void
    {
        $recipient = User::factory()->create([
            'email' => 'recipient@example.com',
        ]);
        $invitation = WorkspaceInvitation::factory()->create([
            'user_id' => $recipient->id,
            'email' => $recipient->email,
        ]);

        Sanctum::actingAs($recipient);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/reject");

        $response->assertOk();
        $response->assertJsonPath('message', 'Invitation rejected successfully.');
        $this->assertNotNull($invitation->fresh()->rejected_at);
    }

    public function test_owner_can_cancel_invitation(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $invitation = WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'invited_by' => $owner->id,
        ]);

        Sanctum::actingAs($owner);

        $response = $this->postJson("/api/v1/invitations/{$invitation->id}/cancel");

        $response->assertOk();
        $response->assertJsonPath('message', 'Invitation cancelled successfully.');
        $this->assertNotNull($invitation->fresh()->cancelled_at);
    }

    public function test_owner_can_resend_invitation(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $invitation = WorkspaceInvitation::factory()->create([
            'workspace_id' => $workspace->id,
            'invited_by' => $owner->id,
            'email' => 'invited@example.com',
            'rejected_at' => now(),
        ]);
        $oldToken = $invitation->token;

        Sanctum::actingAs($owner);

        $response = $this->postJson("/api/v1/invitations/{$invitation->id}/resend");

        $response->assertOk();
        $response->assertJsonPath('message', 'Invitation resent successfully.');

        $freshInvitation = $invitation->fresh();
        $this->assertNotSame($oldToken, $freshInvitation->token);
        $this->assertNull($freshInvitation->rejected_at);
        $this->assertTrue($freshInvitation->expires_at->isFuture());

        Mail::assertSent(
            WorkspaceInvitationMail::class,
            fn (WorkspaceInvitationMail $mail) => $mail->hasTo('invited@example.com')
        );
    }
}
