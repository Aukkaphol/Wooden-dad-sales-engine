<?php

namespace App\Http\Requests\Assets;

use App\Models\Asset;
use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('asset')) ?? false;
    }

    public function rules(): array
    {
        return [
            'brand_id' => [
                'required',
                'uuid',
                Rule::exists('brands', 'id')->where('workspace_id', $this->route('workspace')?->getKey()),
            ],
            'name' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:102400', 'mimetypes:'.implode(',', $this->allowedMimeTypes())],
            'category' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(Asset::STATUSES)],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function brand(): Brand
    {
        return Brand::query()->where('workspace_id', $this->route('workspace')->getKey())->findOrFail($this->string('brand_id')->toString());
    }

    private function allowedMimeTypes(): array
    {
        return [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/svg+xml',
            'video/mp4',
            'video/quicktime',
            'video/webm',
            'audio/mpeg',
            'audio/mp4',
            'audio/wav',
            'audio/x-wav',
            'audio/ogg',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
            'application/zip',
            'application/x-zip-compressed',
        ];
    }
}
