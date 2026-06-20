@extends('layouts.admin', ['title' => 'รีวิวลูกค้า | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">การตลาด</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">รีวิวลูกค้า</h1>
                <p class="mt-2 text-sm text-pine-700">จัดการรีวิวลูกค้า คะแนนดาว ข้อความ และรูปผลงานที่แสดงบนหน้าเว็บไซต์</p>
            </div>
            <a href="{{ route('reviews.index') }}" class="w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดูหน้ารีวิว</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('admin.marketing.reviews.store') }}" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
                <div class="grid gap-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <label>
                            <span class="text-sm font-semibold text-ink">ชื่อลูกค้า</span>
                            <input name="customer_name" value="{{ old('customer_name') }}" placeholder="สมชาย" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        </label>
                        <label>
                            <span class="text-sm font-semibold text-ink">จังหวัด</span>
                            <input name="province" value="{{ old('province') }}" placeholder="เชียงใหม่" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        </label>
                    </div>
                    <label>
                        <span class="text-sm font-semibold text-ink">คะแนนรีวิว</span>
                        <select name="rating" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}" @selected((int) old('rating', 5) === $rating)>{{ str_repeat('★', $rating) }} {{ $rating }} ดาว</option>
                            @endfor
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ข้อความรีวิว</span>
                        <textarea name="review_text" rows="4" placeholder="งานสวยมาก ส่งตรงเวลา" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">{{ old('review_text') }}</textarea>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="active" value="1" checked class="rounded border-pine-300">
                        <span class="text-sm font-semibold text-ink">เปิดแสดงผลบนเว็บไซต์</span>
                    </label>
                </div>
                <div>
                    <p class="text-sm font-semibold text-ink">รูปภาพผลงาน</p>
                    <div class="mt-2 aspect-[4/3] overflow-hidden rounded-lg bg-[linear-gradient(135deg,#d8b47a,#fff7ed,#b7793b)] ring-1 ring-pine-200">
                        <div data-review-preview="new" class="flex h-full w-full items-center justify-center text-sm font-semibold text-white/90">ยังไม่มีรูป</div>
                    </div>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-review-input="new" class="mt-3 block w-full text-sm text-pine-700">
                    <p class="mt-2 text-xs text-pine-600">รองรับ jpg, png, webp ขนาดไม่เกิน 5MB</p>
                    <button class="mt-5 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">เพิ่มรีวิวลูกค้า</button>
                </div>
            </div>
        </form>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Customer Reviews</p>
                    <h2 class="mt-1 text-xl font-semibold text-ink">รีวิวทั้งหมด</h2>
                </div>
                <p class="text-sm text-pine-700">ทั้งหมด {{ number_format($reviews->count()) }} รีวิว</p>
            </div>

            @if ($reviews->isNotEmpty())
                <div class="grid gap-5 lg:grid-cols-2">
                    @foreach ($reviews as $review)
                        <article class="rounded-lg bg-pine-50 p-5 ring-1 ring-pine-200">
                            <div class="grid gap-5 md:grid-cols-[180px_1fr]">
                                <div>
                                    <div class="aspect-[4/3] overflow-hidden rounded-lg bg-[linear-gradient(135deg,#d8b47a,#fff7ed,#b7793b)] ring-1 ring-pine-200">
                                        @if ($review->image_path)
                                            <img data-review-preview="{{ $review->id }}" src="{{ asset('storage/'.$review->image_path) }}" alt="ผลงานของ {{ $review->customer_name }}" class="h-full w-full object-cover">
                                        @else
                                            <div data-review-preview="{{ $review->id }}" class="flex h-full w-full items-center justify-center text-sm font-semibold text-white/90">ยังไม่มีรูป</div>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <form method="post" action="{{ route('admin.marketing.reviews.update', $review) }}" enctype="multipart/form-data" class="grid gap-3">
                                        @csrf
                                        @method('patch')
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <label>
                                                <span class="text-xs font-semibold text-ink">ชื่อลูกค้า</span>
                                                <input name="customer_name" value="{{ old('customer_name', $review->customer_name) }}" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                            </label>
                                            <label>
                                                <span class="text-xs font-semibold text-ink">จังหวัด</span>
                                                <input name="province" value="{{ old('province', $review->province) }}" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                            </label>
                                        </div>
                                        <label>
                                            <span class="text-xs font-semibold text-ink">คะแนน</span>
                                            <select name="rating" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                                @for ($rating = 5; $rating >= 1; $rating--)
                                                    <option value="{{ $rating }}" @selected((int) old('rating', $review->rating) === $rating)>{{ str_repeat('★', $rating) }} {{ $rating }} ดาว</option>
                                                @endfor
                                            </select>
                                        </label>
                                        <label>
                                            <span class="text-xs font-semibold text-ink">ข้อความรีวิว</span>
                                            <textarea name="review_text" rows="3" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">{{ old('review_text', $review->review_text) }}</textarea>
                                        </label>
                                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-review-input="{{ $review->id }}" class="block w-full text-sm text-pine-700">
                                        <label class="flex items-center gap-3">
                                            <input type="checkbox" name="active" value="1" @checked($review->active) class="rounded border-pine-300">
                                            <span class="text-sm font-semibold text-ink">เปิดแสดงผล</span>
                                        </label>
                                        <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">บันทึกรีวิว</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.marketing.reviews.destroy', $review) }}" class="mt-3">
                                        @csrf
                                        @method('delete')
                                        <button class="w-full rounded-md bg-white px-4 py-2 text-sm font-semibold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50">ลบรีวิวนี้</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-pine-300 p-8 text-center text-pine-700">ยังไม่มีรีวิวลูกค้า</div>
            @endif
        </section>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-review-input]').forEach((input) => {
            input.addEventListener('change', () => {
                const key = input.dataset.reviewInput;
                const preview = document.querySelector(`[data-review-preview="${key}"]`);
                const file = input.files?.[0];

                if (!preview || !file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    const image = document.createElement('img');
                    image.src = event.target.result;
                    image.alt = file.name;
                    image.className = 'h-full w-full object-cover';
                    preview.replaceWith(image);
                    image.dataset.reviewPreview = key;
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
@endsection
