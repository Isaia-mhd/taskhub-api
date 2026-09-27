<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderPriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'priorities' => ['required', 'array'],
            'priorities.*.id' => ['required', 'integer'],
            'priorities.*.level' => ['required', 'integer', 'min:0'],
        ];
    }
}
