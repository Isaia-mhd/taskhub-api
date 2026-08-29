<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
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

    public function authenticate(): User
    {

        $this->checkRateLimit();

        $user = User::where('email', $this->input('email'))->first();
        
        if (!$user || !Hash::check($this->input('password'), $user->password)) {

            RateLimiter::hit($this->throttleKey(), $decaySeconds = 900);

            throw ValidationException::withMessages([
                'email' => 'Incorrect credentials.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $user;

    }

    public function checkRateLimit()
    {
        if(RateLimiter::tooManyAttempts($this->throttleKey(), 5)){
            $seconds = RateLimiter::availableIn($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Too many attemps. Try again in ' . $seconds . ' seconds.'
            ]);
        }
    }

    public function throttleKey()
    {
        return strtolower($this->input('email') . '|' . $this->ip());
    }
}
