<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folders' => ['required', 'array'],
            'folders.*.id' => ['required', 'string'],
            'folders.*.position' => ['required', 'integer'],
        ];
    }
}
