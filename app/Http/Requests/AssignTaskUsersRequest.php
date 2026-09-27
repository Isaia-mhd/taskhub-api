<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTaskUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'string', 'distinct', 'exists:users,id'],
        ];
    }
}
