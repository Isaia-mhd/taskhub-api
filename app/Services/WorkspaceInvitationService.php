<?php

namespace App\Services;

use App\Interfaces\MailInterface;
use App\Mail\WorkspaceInvitationMail;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WorkspaceInvitationService
{
    public function __construct(private MailInterface $mailService){}

    
    public function send(Workspace $workspace, User $invitedBy, array $data): WorkspaceInvitation
    {
        return DB::transaction(function () use ($workspace,$invitedBy,$data) 
        {
            $invitedUser = User::where('email',$data['email'])->first();

            if ($invitedUser?->id === $workspace->owner_id) {
                throw ValidationException::withMessages([
                    'email' => 'The workspace owner cannot be invited.',
                ]);
            }

            if ($invitedUser && $workspace->members()->whereKey($invitedUser->id)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This user is already a workspace member.',
                ]);
            }

            $hasPendingInvitation = $workspace->invitations()
                ->where('email', $data['email'])
                ->whereNull('accepted_at')
                ->whereNull('rejected_at')
                ->whereNull('cancelled_at')
                ->where('expires_at', '>', now())
                ->exists();

            if ($hasPendingInvitation) {
                throw ValidationException::withMessages([
                    'email' => 'This email already has a pending invitation.',
                ]);
            }

            $invitation = WorkspaceInvitation::create([
                'workspace_id' => $workspace->id,
                'invited_by' => $invitedBy->id,
                'user_id' => $invitedUser?->id,
                'email' => $data['email'],
                'role' => $data['role'],
                'token' => Str::random(64),
                'expires_at' => now()->addDays(7),
            ]);

            $this->mailService->send(
                $invitation->email,
                new WorkspaceInvitationMail($invitation)
            );

            return $invitation;
        });
    }

    
    public function getPending(Workspace $workspace): LengthAwarePaginator
    {
        return $workspace->invitations()
            ->with(['workspace', 'inviter', 'user'])
            ->whereNull('accepted_at')
            ->whereNull('rejected_at')
            ->whereNull('cancelled_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->paginate(10);
    }

    
    public function get(WorkspaceInvitation $invitation): WorkspaceInvitation 
    {
        return $invitation->load([
            'workspace',
            'inviter',
            'user',
        ]);
    }

   
    public function accept(WorkspaceInvitation $invitation,User $user): void 
    {
        DB::transaction(function () use ($invitation, $user) {
            $this->ensurePending($invitation);
            $this->ensureRecipient($invitation, $user);

            $invitation->workspace
                ->members()
                ->syncWithoutDetaching([
                    $user->id => [
                        'role' => $invitation->role,
                        'joined_at' => now(),
                    ],
                ]);

            $invitation->update([
                'user_id' => $user->id,
                'accepted_at' => now(),
            ]);
        });
    }

    
    public function reject(WorkspaceInvitation $invitation, User $user): void 
    {
        $this->ensurePending($invitation);
        $this->ensureRecipient($invitation, $user);

        $invitation->update([
            'user_id' => $user->id,
            'rejected_at' => now(),
        ]);
    }

    
    public function cancel(WorkspaceInvitation $invitation): void 
    {
        $this->ensurePending($invitation);

        $invitation->update([
            'cancelled_at' => now(),
        ]);
    }

    public function resend(WorkspaceInvitation $invitation): WorkspaceInvitation 
    {
        if ($invitation->accepted_at !== null) {
            throw ValidationException::withMessages([
                'invitation' => 'Accepted invitations cannot be resent.',
            ]);
        }

        $invitation->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
            'rejected_at' => null,
            'cancelled_at' => null,
        ]);

        $this->mailService->send(
            $invitation->email,
            new WorkspaceInvitationMail($invitation)
        );

        return $invitation;
    }

    private function ensurePending(WorkspaceInvitation $invitation): void
    {
        if (!$invitation->isPending()) {
            throw ValidationException::withMessages([
                'invitation' => 'This invitation is no longer pending.',
            ]);
        }
    }

    private function ensureRecipient(WorkspaceInvitation $invitation, User $user): void
    {
        if (strtolower($invitation->email) !== strtolower($user->email)) {
            abort(403, 'This invitation belongs to another user.');
        }
    }
}
