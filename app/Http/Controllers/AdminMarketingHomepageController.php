<?php

namespace App\Http\Controllers;

use App\Models\FurnitureSetCategory;
use App\Models\WebsiteSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminMarketingHomepageController extends Controller
{
    public function index(): View
    {
        return view('admin.marketing.homepage', [
            'sections' => WebsiteSection::query()
                ->orderBy('sort_order')
                ->get()
                ->keyBy('section_key'),
            'categories' => FurnitureSetCategory::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function updateSection(Request $request, WebsiteSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->messages());

        $payload = [
            'title' => $validated['title'] ?? null,
            'subtitle' => $validated['subtitle'] ?? null,
            'description' => $validated['description'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'button_url' => $validated['button_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $section->sort_order,
            'active' => (bool) ($validated['active'] ?? false),
        ];

        if ($request->hasFile('image')) {
            $payload['image_path'] = $this->storeHomepageImage($request, $section->image_path);
        }

        $section->update($payload);

        return back()->with('success', 'บันทึกส่วนหน้าแรกเรียบร้อยแล้ว');
    }

    public function updateCategory(Request $request, FurnitureSetCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:2000'],
            'full_description' => ['nullable', 'string', 'max:5000'],
            'start_price' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->messages());

        $payload = [
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'full_description' => $validated['full_description'] ?? null,
            'start_price' => $validated['start_price'] ?? null,
            'sort_order' => $validated['sort_order'],
            'active' => (bool) ($validated['active'] ?? false),
        ];

        if ($request->hasFile('image')) {
            $payload['image_path'] = $this->storeHomepageImage($request, $category->image_path);
        }

        $category->update($payload);

        return back()->with('success', 'บันทึกหมวดเซ็ตเฟอร์นิเจอร์เรียบร้อยแล้ว');
    }

    private function storeHomepageImage(Request $request, ?string $oldPath): string
    {
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return $request->file('image')->store('homepage', 'public');
    }

    private function messages(): array
    {
        return [
            'image.mimes' => 'กรุณาอัปโหลดไฟล์รูปภาพ jpg, png หรือ webp เท่านั้น',
            'image.max' => 'ขนาดไฟล์ต้องไม่เกิน 5MB',
            'image.file' => 'กรุณาอัปโหลดไฟล์รูปภาพ jpg, png หรือ webp เท่านั้น',
        ];
    }
}
