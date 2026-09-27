<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:0'],
            'color' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
        ];
    }
}
