<?php

namespace App\Http\Controllers;

use App\Models\CustomerReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminCustomerReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.marketing.reviews', [
            'reviews' => CustomerReview::query()
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'active' => ['nullable', 'boolean'],
        ], $this->messages());

        CustomerReview::create([
            'customer_name' => $validated['customer_name'],
            'province' => $validated['province'] ?? null,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
            'image_path' => $request->hasFile('image') ? $request->file('image')->store('reviews', 'public') : null,
            'active' => (bool) ($validated['active'] ?? true),
        ]);

        return back()->with('success', 'เพิ่มรีวิวลูกค้าเรียบร้อยแล้ว');
    }

    public function update(Request $request, CustomerReview $review): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'active' => ['nullable', 'boolean'],
        ], $this->messages());

        $payload = [
            'customer_name' => $validated['customer_name'],
            'province' => $validated['province'] ?? null,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
            'active' => (bool) ($validated['active'] ?? false),
        ];

        if ($request->hasFile('image')) {
            if ($review->image_path) {
                Storage::disk('public')->delete($review->image_path);
            }

            $payload['image_path'] = $request->file('image')->store('reviews', 'public');
        }

        $review->update($payload);

        return back()->with('success', 'อัปเดตรีวิวลูกค้าเรียบร้อยแล้ว');
    }

    public function destroy(CustomerReview $review): RedirectResponse
    {
        if ($review->image_path) {
            Storage::disk('public')->delete($review->image_path);
        }

        $review->delete();

        return back()->with('success', 'ลบรีวิวลูกค้าเรียบร้อยแล้ว');
    }

    private function messages(): array
    {
        return [
            'customer_name.required' => 'กรุณากรอกชื่อลูกค้า',
            'rating.required' => 'กรุณาเลือกคะแนนรีวิว',
            'rating.min' => 'คะแนนรีวิวต้องอยู่ระหว่าง 1-5 ดาว',
            'rating.max' => 'คะแนนรีวิวต้องอยู่ระหว่าง 1-5 ดาว',
            'review_text.required' => 'กรุณากรอกข้อความรีวิว',
            'image.mimes' => 'กรุณาอัปโหลดไฟล์รูปภาพ jpg, png หรือ webp เท่านั้น',
            'image.max' => 'ขนาดไฟล์ต้องไม่เกิน 5MB',
            'image.file' => 'กรุณาอัปโหลดไฟล์รูปภาพ jpg, png หรือ webp เท่านั้น',
        ];
    }
}
