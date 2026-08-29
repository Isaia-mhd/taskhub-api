<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use JsonResponseTrait;
    public function login(UserLoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();
        $tokenName = $request->userAgent() ?: $user->name;

        return $this->successJson('Connected with success', [
            'user' => new UserResource($user),
            'token' => $user->createToken($tokenName)->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }   

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successJson('Disconnected with success');
    }
}
