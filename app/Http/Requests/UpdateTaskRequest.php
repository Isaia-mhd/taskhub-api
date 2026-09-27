<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'position' => ['nullable', 'integer'],
            'estimate_minutes' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
