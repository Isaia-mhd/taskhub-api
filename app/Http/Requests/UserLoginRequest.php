<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    // public function authenticate(): User | JsonResponse
    // {

    //     $this->checkRateLimit();

    //     // $user = User::where('email', $this->input('email'))->first();
        
    //     if (!Auth::attempt([$this->input('email'), $this->input('password')])) {

    //         RateLimiter::hit($this->throttleKey(), $decaySeconds = 900);

    //         return response()->json([
    //             'Invalid credentials.'
    //         ], 403);
    //     }

    //     RateLimiter::clear($this->throttleKey());

    //     return Auth::user();

    // }

    // public function checkRateLimit()
    // {
    //     if(RateLimiter::tooManyAttempts($this->throttleKey(), 5)){
    //         $seconds = RateLimiter::availableIn($this->throttleKey());

    //         throw ValidationException::withMessages([
    //             'email' => 'Too many attemps. Try again in ' . $seconds . ' seconds.'
    //         ]);
    //     }
    // }

    // public function throttleKey()
    // {
    //     return strtolower($this->input('email') . '|' . $this->ip());
    // }
}
