<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use JsonResponseTrait;
    public function login(UserLoginRequest $request): JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return $this->successJson('Connected with success', $request->user());
    }   

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->successJson('Disconnected with success');
    }
}
