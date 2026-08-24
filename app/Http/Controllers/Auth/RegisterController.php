<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
class RegisterController extends Controller
{
    use JsonResponseTrait;
    public function __construct(private UserService $userService){}

    /**
     * Store a newly created resource in storage.
     */

    public function store(UserRegisterRequest $request): JsonResponse
    {
        $user = $this->userService->store($request->validated());
        
        return $this->successJson('User registered successfully.', new UserResource($user), 201);
    
    }
}
