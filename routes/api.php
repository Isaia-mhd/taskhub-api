<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceInvitationController;
use App\Http\Controllers\WorkspaceMemberController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['throttle:6,1'], 'prefix' => 'v1'], function () {
    // Public routes go here
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/invitations/{invitation:token}', [WorkspaceInvitationController::class, 'show']);
});


Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function () {
    // Protected routes go here

    // Users
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('workspaces.spaces');

        return response()->json([
            'user' => new UserResource($user)
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
    Route::put('users/{user}/avatar', [UserController::class, 'updateAvatar']);

    Route::apiResource('workspaces', WorkspaceController::class);
    Route::get('workspaces/{workspace}/invitations', [WorkspaceInvitationController::class, 'index']);
    Route::post('workspaces/{workspace}/invitations', [WorkspaceInvitationController::class, 'store']);
    Route::apiResource('workspace-members', WorkspaceMemberController::class);
    Route::get('workspace-members/{workspace}/members/', [WorkspaceMemberController::class, 'members']);
    Route::put('workspace-members/{workspace}/members/{member}/role', [WorkspaceMemberController::class, 'changeMemberRole']);

    Route::post('invitations/{invitation:token}/accept', [WorkspaceInvitationController::class, 'accept']);
    Route::post('invitations/{invitation:token}/reject', [WorkspaceInvitationController::class, 'reject']);
    Route::post('invitations/{invitation}/cancel', [WorkspaceInvitationController::class, 'cancel']);
    Route::post('invitations/{invitation}/resend', [WorkspaceInvitationController::class, 'resend']);
    Route::apiResource('spaces', SpaceController::class);


    });
    Route::get('workspaces/{workspace}/spaces', [SpaceController::class, 'getByWorkspace']);
    Route::patch('workspaces/{workspace}/spaces/reorder', [SpaceController::class, 'reorder']);