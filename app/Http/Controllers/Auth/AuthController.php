<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use JsonResponseTrait;
    public function login(UserLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if(!Auth::attempt($validated))
        {
            return $this->errorJson('Invalid credentials', null, 422);
        }

        $request->session()->regenerate();

        return $this->successJson(
            'Connected with success',
            new UserResource(Auth::user())
        );
    }   

    public function logout(Request $request): JsonResponse
    {
        // Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successJson('Disconnected with success');
    }
}
