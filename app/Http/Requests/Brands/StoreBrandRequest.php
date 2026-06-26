<?php

namespace App\Http\Requests\Brands;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [\App\Models\Brand::class, $this->route('workspace')]) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_family' => ['nullable', 'string', 'max:255'],
            'tone' => ['nullable', 'string', 'max:255'],
            'voice' => ['nullable', 'string', 'max:5000'],
            'default_prompt' => ['nullable', 'string', 'max:10000'],
            'default_cta' => ['nullable', 'string', 'max:255'],
            'contact_information' => ['nullable', 'array'],
            'contact_information.email' => ['nullable', 'email:rfc', 'max:255'],
            'contact_information.phone' => ['nullable', 'string', 'max:50'],
            'contact_information.website' => ['nullable', 'url', 'max:255'],
            'contact_information.address' => ['nullable', 'string', 'max:1000'],
            'social_links' => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_links.tiktok' => ['nullable', 'url', 'max:255'],
        ];
    }
}
