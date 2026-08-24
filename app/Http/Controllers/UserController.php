<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use JsonResponseTrait;
    public function __construct(private UserService $userService){}


    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user = $this->userService->update($user, $validated);

        return $this->successJson('User updated successfully.', new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->userService->deleteAccount($user);

        return $this->successJson('User deleted successfully.');
    }

    public function updateAvatar(UpdateAvatarRequest $request, User $user)
    {
        $this->userService->updateAvatar($user, $request->file('avatar'));

        return $this->successJson('Avatar updated successfully.');
    }
}
