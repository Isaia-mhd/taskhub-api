<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkspaceInvitationRequest;
use App\Http\Resources\WorkspaceInvitationResource;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Services\WorkspaceInvitationService;
use App\Traits\JsonResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceInvitationController extends Controller
{
    use AuthorizesRequests, JsonResponseTrait;

    public function __construct(private WorkspaceInvitationService $workspaceInvitationService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('invite', $workspace);

        return $this->successJson(
            'Pending invitations loaded.',
            WorkspaceInvitationResource::collection($this->workspaceInvitationService->getPending($workspace))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceInvitationRequest $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('invite', $workspace);

        $invitation = $this->workspaceInvitationService->send($workspace, $request->user(),$request->validated());

        return $this->successJson(
            'Invitation sent successfully.',
            new WorkspaceInvitationResource($invitation),
            201
        );

    }

    /**
     * Display the specified resource.
     */
    public function show(WorkspaceInvitation $invitation): JsonResponse
    {
        return $this->successJson(
            'Invitation loaded.',
            new WorkspaceInvitationResource($this->workspaceInvitationService->get($invitation))
        );
    }

    public function accept(Request $request, WorkspaceInvitation $invitation): JsonResponse
    {
        $this->workspaceInvitationService->accept($invitation, $request->user());

        return $this->successJson('Invitation accepted successfully.');
    }

    public function reject(Request $request, WorkspaceInvitation $invitation): JsonResponse
    {
        $this->workspaceInvitationService->reject($invitation, $request->user());

        return $this->successJson('Invitation rejected successfully.');
    }

    public function cancel(WorkspaceInvitation $invitation): JsonResponse
    {
        $this->authorize('invite', $invitation->workspace);

        $this->workspaceInvitationService->cancel($invitation);

        return $this->successJson('Invitation cancelled successfully.');
    }

    public function resend(WorkspaceInvitation $invitation): JsonResponse
    {
        $this->authorize('invite', $invitation->workspace);

        return $this->successJson(
            'Invitation resent successfully.',
            new WorkspaceInvitationResource($this->workspaceInvitationService->resend($invitation)),
        );
    }
}
