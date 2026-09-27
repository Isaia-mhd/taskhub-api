<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTaskListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_lists' => ['required', 'array'],
            'task_lists.*.id' => ['required', 'string'],
            'task_lists.*.position' => ['required', 'integer'],
        ];
    }
}
