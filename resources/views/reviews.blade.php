@extends('layouts.public', ['title' => __('messages.nav.reviews').' | '.company()->display_name])

@php($en = app()->getLocale() === 'en')

@section('content')
<section class="bg-[linear-gradient(120deg,#fbf7ef_0%,#ffffff_55%,#f0dfc3_100%)]">
    <div class="mx-auto max-w-6xl px-5 py-14">
        <p class="text-sm font-semibold text-pine-500">Customer Reviews</p>
        <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-semibold text-ink md:text-5xl">{{ $en ? 'Customer stories from '.company()->display_name : 'รีวิวจากลูกค้า '.company()->display_name }}</h1>
                <p class="mt-4 max-w-2xl leading-8 text-pine-700">{{ $en ? 'Feedback from customers who ordered custom pine wood furniture for bedrooms, living rooms, dining rooms, and workspaces.' : 'เสียงตอบรับจากลูกค้าที่สั่งทำเฟอร์นิเจอร์ไม้สน ทั้งงานห้องนอน ห้องนั่งเล่น ห้องอาหาร และห้องทำงาน' }}</p>
            </div>
            <a href="{{ route('lead.create') }}" class="inline-flex w-fit items-center justify-center rounded-full bg-pine-700 px-6 py-3 text-sm font-semibold text-white hover:bg-pine-500">{{ __('messages.hero.cta_primary') }}</a>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-6xl px-5 py-12">
        @if ($reviews->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($reviews as $review)
                    <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-pine-200">
                        @if ($review->image_path)
                            <img src="{{ asset('storage/'.$review->image_path) }}" alt="{{ $review->customer_name }}" class="aspect-[4/3] w-full object-cover">
                        @else
                            <div class="aspect-[4/3] bg-[linear-gradient(135deg,#d8b47a,#fff7ed,#ffffff)] p-6">
                                <div class="h-full rounded-md bg-white/45 p-4">
                                    <div class="h-1/2 rounded bg-pine-200"></div>
                                    <div class="mt-4 h-8 rounded bg-pine-300"></div>
                                    <div class="mt-3 h-8 w-2/3 rounded bg-white/80"></div>
                                </div>
                            </div>
                        @endif
                        <div class="p-5">
                            <p class="text-lg tracking-wide text-amber-500">{{ str_repeat('★', $review->rating) }}<span class="text-pine-200">{{ str_repeat('★', 5 - $review->rating) }}</span></p>
                            <p class="mt-4 min-h-24 text-lg leading-8 text-ink">"{{ $review->review_text }}"</p>
                            <div class="mt-5 border-t border-pine-100 pt-4">
                                <p class="font-semibold text-ink">{{ $en ? 'Khun ' : 'คุณ' }}{{ $review->customer_name }}</p>
                                @if ($review->province)
                                    <p class="mt-1 text-sm text-pine-700">{{ $en ? $review->province : 'จังหวัด'.$review->province }}</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-lg border border-dashed border-pine-300 bg-pine-50 p-10 text-center">
                <h2 class="text-xl font-semibold text-ink">{{ $en ? 'No customer reviews yet' : 'ยังไม่มีรีวิวลูกค้า' }}</h2>
                <p class="mt-2 text-pine-700">{{ $en ? 'When admins add active reviews, they will appear here automatically.' : 'เมื่อแอดมินเพิ่มรีวิวที่เปิดแสดงผล รีวิวจะแสดงในหน้านี้โดยอัตโนมัติ' }}</p>
            </div>
        @endif
    </div>
</section>
@endsection
