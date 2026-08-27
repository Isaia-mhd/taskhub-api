<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_workspaces(): void
    {
        $user = User::factory()->create();

        Workspace::factory()->count(3)->create([
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user, 'web');

        $response = $this->getJson('/api/v1/workspaces');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data',
        ]);
    }

    public function test_guest_cannot_get_workspaces(): void
    {
        $response = $this->getJson('/api/v1/workspaces');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_workspace(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'web');

        $response = $this->postJson('/api/v1/workspaces', [
            'name' => 'My Workspace',
            'description' => 'My workspace description',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Workspace created successfully.',
        ]);

        $this->assertDatabaseHas('workspaces', [
            'owner_id' => $user->id,
            'name' => 'My Workspace',
            'description' => 'My workspace description',
        ]);
    }

    public function test_authenticated_user_can_get_his_workspace(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user, 'web');

        $response = $this->getJson(
            "/api/v1/workspaces/{$workspace->id}"
        );

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'A workspace loaded.',
        ]);
    }

    public function test_user_cannot_get_workspace_owned_by_another_user(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $otherUser->id,
        ]);

        $this->actingAs($user, 'web');

        $response = $this->getJson(
            "/api/v1/workspaces/{$workspace->id}"
        );

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_update_workspace(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
            'name' => 'Old name',
        ]);

        $this->actingAs($user, 'web');

        $response = $this->putJson(
            "/api/v1/workspaces/{$workspace->id}",
            [
                'name' => 'New name',
                'description' => 'Updated description',
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
            'name' => 'New name',
            'description' => 'Updated description',
        ]);
    }

    public function test_authenticated_user_can_delete_workspace(): void
    {
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user, 'web');

        $response = $this->deleteJson(
            "/api/v1/workspaces/{$workspace->id}"
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('workspaces', [
            'id' => $workspace->id,
        ]);
    }

    public function test_owner_can_add_new_member(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($owner, 'web');

        $response = $this->postJson("/api/v1/workspace-members", [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('workspace_members', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id
        ]);

    }

    public function test_only_owner_can_add_member(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $notOwner = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id
        ]);

        $this->actingAs($notOwner, 'web');

        $response = $this->postJson("/api/v1/workspace-members", [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('workspace_members', [
            'user_id' => $user->id,
            'workspace_id' => $workspace->id
        ]);
    }

    public function test_owner_can_remove_member(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($owner, 'web');

        $this->postJson("/api/v1/workspace-members", [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson("/api/v1/workspace-members/{$workspace->id}", [
            'user_id' => $user->id
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('workspace_members', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id
        ]);

    }

    public function test_owner_can_change_role_of_member(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id
        ]);

        $this->actingAs($owner, 'web');

        $response = $this->postJson("/api/v1/workspace-members", [
            'workspace_id' => $workspace->id,
            'user_id' => $member->id
        ]);

        $response->assertStatus(200);

        $response1 = $this->putJson("/api/v1/workspace-members/{$workspace->id}/members/{$member->id}/role", [
            'role' => 'admin'
        ]);

        $this->assertDatabaseHas('workspace_members', [
          'workspace_id' => $workspace->id,
          'user_id' => $member->id,  
          'role' => 'admin',  
        ]);

        $response1->assertStatus(200);
    }

      public function test_owner_can_see_all_members(): void
    {
        $owner = User::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();

        $workspace = Workspace::factory()->create([
            'owner_id' => $owner->id
        ]);

        $this->actingAs($owner, 'web');

        $response = $this->postJson("/api/v1/workspace-members", [
            'workspace_id' => $workspace->id,
            'user_id' => $member1->id
        ]);

        $response->assertStatus(200);

        $response = $this->postJson("/api/v1/workspace-members", [
            'workspace_id' => $workspace->id,
            'user_id' => $member2->id
        ]);

        $response->assertStatus(200);

        $response = $this->getJson("/api/v1/workspace-members/{$workspace->id}/members/");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('workspace_members', [
          'workspace_id' => $workspace->id,
          'user_id' => $member1->id,
        ]);

        $this->assertDatabaseHas('workspace_members', [
          'workspace_id' => $workspace->id,
          'user_id' => $member2->id, 
        ]);

    }
}
