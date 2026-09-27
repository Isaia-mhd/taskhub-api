<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:0'],
            'color' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }
}
