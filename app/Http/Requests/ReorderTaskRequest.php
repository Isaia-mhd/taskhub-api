<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tasks' => ['required', 'array'],
            'tasks.*.id' => ['required', 'string'],
            'tasks.*.position' => ['required', 'integer'],
        ];
    }
}
