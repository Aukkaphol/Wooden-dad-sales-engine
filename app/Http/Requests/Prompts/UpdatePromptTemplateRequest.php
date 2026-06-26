<?php

namespace App\Http\Requests\Prompts;

use App\Models\Brand;
use App\Models\PromptTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromptTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('prompt')) ?? false;
    }

    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'uuid', Rule::exists('brands', 'id')->where('workspace_id', $this->route('workspace')?->getKey())],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(PromptTemplate::CATEGORIES)],
            'platform' => ['required', Rule::in(PromptTemplate::PLATFORMS)],
            'prompt_template' => ['required', 'string', 'max:20000'],
            'variables' => ['nullable', 'string', 'max:2000'],
            'example_output' => ['nullable', 'string', 'max:20000'],
            'status' => ['required', Rule::in(PromptTemplate::STATUSES)],
            'tags' => ['nullable', 'string', 'max:1000'],
            'favorite' => ['nullable', 'boolean'],
            'recommended_model' => ['nullable', Rule::in(PromptTemplate::MODELS)],
        ];
    }

    public function brand(): Brand
    {
        return Brand::query()->where('workspace_id', $this->route('workspace')->getKey())->findOrFail($this->string('brand_id')->toString());
    }
}
