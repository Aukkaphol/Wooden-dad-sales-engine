<?php

namespace App\Http\Requests\Contents;

use Illuminate\Foundation\Http\FormRequest;

class PreviewGeneratedContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view', $this->route('content')) ?? false;
    }

    public function rules(): array
    {
        return [
            'variables' => ['nullable', 'array'],
            'variables.*' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
