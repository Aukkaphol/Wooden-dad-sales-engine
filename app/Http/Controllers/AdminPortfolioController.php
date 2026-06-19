<?php

namespace App\Http\Controllers;

use App\Models\PortfolioImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminPortfolioController extends Controller
{
    public function index(): View
    {
        return view('admin.portfolio.index', [
            'categories' => PortfolioImage::CATEGORIES,
            'portfolioImages' => PortfolioImage::query()
                ->orderBy('sort_order')
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(PortfolioImage::CATEGORIES))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->messages());

        foreach ($request->file('images', []) as $image) {
            PortfolioImage::create([
                'title' => $validated['title'] ?? null,
                'category' => $validated['category'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'active' => (bool) ($validated['active'] ?? true),
                'image_path' => $image->store('portfolio', 'public'),
            ]);
        }

        return back()->with('success', 'เพิ่มรูปผลงานเรียบร้อยแล้ว');
    }

    public function update(Request $request, PortfolioImage $portfolioImage): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(PortfolioImage::CATEGORIES))],
            'sort_order' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ], $this->messages());

        $portfolioImage->update([
            'title' => $validated['title'] ?? null,
            'category' => $validated['category'],
            'sort_order' => $validated['sort_order'],
            'active' => (bool) ($validated['active'] ?? false),
        ]);

        return back()->with('success', 'อัปเดตรูปผลงานเรียบร้อยแล้ว');
    }

    public function destroy(PortfolioImage $portfolioImage): RedirectResponse
    {
        Storage::disk('public')->delete($portfolioImage->image_path);
        $portfolioImage->delete();

        return back()->with('success', 'ลบรูปผลงานเรียบร้อยแล้ว');
    }

    private function messages(): array
    {
        return [
            'category.required' => 'กรุณาเลือกหมวดผลงาน',
            'category.in' => 'กรุณาเลือกหมวดผลงานให้ถูกต้อง',
            'images.required' => 'กรุณาอัปโหลดรูปผลงานอย่างน้อย 1 รูป',
            'images.array' => 'กรุณาอัปโหลดรูปผลงานอย่างน้อย 1 รูป',
            'images.*.mimes' => 'กรุณาอัปโหลดไฟล์รูปภาพ jpg, png หรือ webp เท่านั้น',
            'images.*.max' => 'ขนาดไฟล์ต้องไม่เกิน 5MB',
            'images.*.file' => 'กรุณาอัปโหลดไฟล์รูปภาพ jpg, png หรือ webp เท่านั้น',
        ];
    }
}
