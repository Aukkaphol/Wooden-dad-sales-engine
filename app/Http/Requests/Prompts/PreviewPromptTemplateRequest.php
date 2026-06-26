<?php

namespace App\Http\Requests\Prompts;

use Illuminate\Foundation\Http\FormRequest;

class PreviewPromptTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view', $this->route('prompt')) ?? false;
    }

    public function rules(): array
    {
        return [
            'values' => ['nullable', 'array'],
            'values.*' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
