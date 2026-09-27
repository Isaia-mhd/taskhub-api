<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:open,closed'],
            'position' => ['nullable', 'integer'],
        ];
    }
}
