<?php

namespace App\Http\Requests\Prompts;

use Illuminate\Foundation\Http\FormRequest;

class RatePromptTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('view', $this->route('prompt')) ?? false;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'successful' => ['nullable', 'boolean'],
        ];
    }
}
