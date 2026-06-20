@extends('layouts.public', ['title' => company()->display_name.' | เฟอร์นิเจอร์ไม้สนสั่งทำสำหรับบ้านของคุณ'])

@php
    $company = company();
    $hero = $sections['hero'] ?? null;
    $heroImage = $hero?->image_path ? asset('storage/'.$hero->image_path) : 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85';
    $portfolioFallbacks = collect([
        ['image' => 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85', 'name' => 'บ้านคุณสมชาย', 'room' => 'ห้องนอน', 'province' => 'กำแพงเพชร'],
        ['image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85', 'name' => 'บ้านคุณอร', 'room' => 'ห้องรับแขก', 'province' => 'เชียงใหม่'],
        ['image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=85', 'name' => 'บ้านคุณกิตติ', 'room' => 'ห้องทำงาน', 'province' => 'นครสวรรค์'],
    ]);
    $portfolioItems = ($latestPortfolioImages ?? collect())->take(6);
    $inspirations = [
        ['title' => 'บ้านมินิมอล', 'image' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'บ้านญี่ปุ่น', 'image' => 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'บ้านโมเดิร์น', 'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'คาเฟ่ไม้สน', 'image' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'ห้องทำงาน', 'image' => 'https://images.unsplash.com/photo-1600494448850-6013c64ba722?auto=format&fit=crop&w=900&q=85'],
    ];
    $reviewFallbacks = collect([
        ['name' => 'สมชาย', 'province' => 'เชียงใหม่', 'rating' => 5, 'text' => 'งานสวยมาก ส่งตรงเวลา ทีมงานเก็บรายละเอียดดีมาก', 'project' => 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85', 'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80'],
        ['name' => 'อร', 'province' => 'ลำพูน', 'rating' => 5, 'text' => 'ช่วยออกแบบให้เข้ากับบ้านจริง ใช้งานง่ายและอบอุ่นมาก', 'project' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85', 'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=300&q=80'],
    ]);
@endphp

@section('content')
<style>
.premium-home-hero > img {
        filter: saturate(1.05) contrast(1.03);
        transform: scale(1.045);
    }

    .premium-home-copy h1 + p {
        margin-top: 2rem;
    }
</style>

<div class="bg-[#fffaf3] pb-24 text-ink md:pb-0">
    <section class="premium-home-hero relative min-h-svh overflow-hidden bg-[#1d1711]">
        <img src="{{ $heroImage }}" alt="เฟอร์นิเจอร์ไม้สนสั่งทำสำหรับบ้านอบอุ่น" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(18,14,10,.76)_0%,rgba(18,14,10,.44)_42%,rgba(18,14,10,.18)_100%)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(18,14,10,.58)_0%,rgba(18,14,10,.18)_38%,rgba(18,14,10,.76)_100%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_68%_36%,rgba(255,255,255,.12)_0%,rgba(255,255,255,0)_34%)]"></div>
        <div class="relative mx-auto flex min-h-svh max-w-7xl flex-col justify-end px-5 pb-12 pt-36 sm:px-6 md:pb-20 lg:px-8">
            <div class="premium-home-copy max-w-[420px] md:max-w-3xl">
                <div class="mb-4 inline-flex rounded-full border border-white/25 bg-white/16 px-4 py-2 text-xs font-semibold text-white shadow-sm backdrop-blur-md">ออกแบบตามพื้นที่จริง ผลิตตามสั่ง ติดตั้งครบจบ</div>
                <h1 class="text-[2.18rem] font-semibold leading-[1.12] tracking-normal text-white drop-shadow-[0_8px_28px_rgba(0,0,0,.32)] sm:text-5xl md:text-[3.45rem] lg:text-[3.8rem]">
                    เฟอร์นิเจอร์ไม้สนสั่งทำ<br>
                    <span class="text-white/95">สำหรับบ้านที่คุณอยากอยู่ไปอีก 20 ปี</span>
                </h1>
                <p class="mt-5 max-w-2xl text-lg font-medium leading-8 text-white/90 md:text-xl md:leading-9">ออกแบบตามพื้นที่จริง ผลิตด้วยไม้สนคุณภาพ พร้อมติดตั้งโดยทีมงานมืออาชีพ</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('lead.create') }}" class="inline-flex min-h-13 items-center justify-center rounded-full bg-white px-7 py-3.5 text-base font-semibold text-pine-800 shadow-[0_20px_55px_rgba(0,0,0,.20)] transition hover:bg-pine-50">ขอแบบและประเมินฟรี</a>
                    <a href="{{ route('portfolio.index') }}" class="inline-flex min-h-13 items-center justify-center rounded-full border border-white/70 bg-white/10 px-7 py-3.5 text-base font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/18">ดูผลงานจริง</a>
                </div>
                <div class="mt-8 grid max-w-xl grid-cols-3 gap-3 text-white">
                    <div class="rounded-2xl border border-white/18 bg-white/12 p-3 backdrop-blur-md">
                        <p class="text-lg font-semibold">Custom</p>
                        <p class="mt-1 text-xs leading-5 text-white/78">ผลิตตามพื้นที่</p>
                    </div>
                    <div class="rounded-2xl border border-white/18 bg-white/12 p-3 backdrop-blur-md">
                        <p class="text-lg font-semibold">Pine</p>
                        <p class="mt-1 text-xs leading-5 text-white/78">ไม้สนโทนอุ่น</p>
                    </div>
                    <div class="rounded-2xl border border-white/18 bg-white/12 p-3 backdrop-blur-md">
                        <p class="text-lg font-semibold">Install</p>
                        <p class="mt-1 text-xs leading-5 text-white/78">พร้อมติดตั้ง</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-[#fffaf3] px-5 py-8">
        <div class="mx-auto grid max-w-6xl gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['icon' => '📐', 'title' => 'วัดพื้นที่จริง', 'text' => 'เข้าใจขนาดและการใช้งานก่อนผลิต'],
                ['icon' => '💰', 'title' => 'เสนอราคาชัดเจน', 'text' => 'คุยงบประมาณและขอบเขตงานตรงไปตรงมา'],
                ['icon' => '🪵', 'title' => 'ผลิตด้วยไม้สนแท้', 'text' => 'โทนอบอุ่น งานสัมผัสจริง ดูแลง่าย'],
                ['icon' => '🛡', 'title' => 'รับประกันงาน', 'text' => 'ดูแลหลังส่งมอบโดยทีมงานเดียวกัน'],
            ] as $trustItem)
                <article class="rounded-[22px] bg-white p-5 shadow-[0_18px_45px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                    <div class="text-3xl">{{ $trustItem['icon'] }}</div>
                    <h2 class="mt-4 text-lg font-semibold text-ink">{{ $trustItem['title'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-pine-700">{{ $trustItem['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="bg-white px-5 py-12 md:py-18">
        <div class="mx-auto max-w-6xl">
            <div class="mb-7 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-pine-500">SHOW THE WORK</p>
                    <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">ผลงานจริงก่อนตัดสินใจ</h2>
                </div>
                <a href="{{ route('portfolio.index') }}" class="hidden rounded-full border border-pine-200 px-5 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50 sm:inline-flex">ดูทั้งหมด</a>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                @forelse ($portfolioItems as $index => $item)
                    <article class="{{ $index === 0 ? 'lg:col-span-2 lg:row-span-2' : '' }} group overflow-hidden rounded-[24px] bg-pine-50 shadow-[0_20px_50px_rgba(93,66,39,.10)]">
                        <div class="{{ $index === 0 ? 'aspect-[4/5] md:aspect-[16/11]' : 'aspect-[4/5]' }} relative">
                            <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title ?: $item->category_name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-5 text-white">
                                <h3 class="text-2xl font-semibold">{{ $item->title ?: 'บ้านลูกค้า Wooden Dad' }}</h3>
                                <p class="mt-1 text-sm text-white/85">{{ $item->category_name }} · งานผลิตตามสั่ง</p>
                            </div>
                        </div>
                    </article>
                @empty
                    @foreach ($portfolioFallbacks as $index => $item)
                        <article class="{{ $index === 0 ? 'lg:col-span-2 lg:row-span-2' : '' }} group overflow-hidden rounded-[24px] bg-pine-50 shadow-[0_20px_50px_rgba(93,66,39,.10)]">
                            <div class="{{ $index === 0 ? 'aspect-[4/5] md:aspect-[16/11]' : 'aspect-[4/5]' }} relative">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-5 text-white">
                                    <h3 class="text-2xl font-semibold">{{ $item['name'] }}</h3>
                                    <p class="mt-1 text-sm text-white/85">{{ $item['room'] }} · {{ $item['province'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-[#fffaf3] px-5 py-12">
        <div class="mx-auto max-w-6xl">
            <div class="mb-7 max-w-2xl">
                <p class="text-sm font-semibold text-pine-500">ROOM INSPIRATION</p>
                <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">เลือกอารมณ์ของพื้นที่</h2>
            </div>
            <div class="flex snap-x gap-4 overflow-x-auto pb-3 lg:grid lg:grid-cols-5 lg:overflow-visible">
                @foreach ($inspirations as $item)
                    <article class="min-w-[78%] snap-start overflow-hidden rounded-[24px] bg-white shadow-[0_18px_42px_rgba(93,66,39,.08)] sm:min-w-[42%] lg:min-w-0">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="aspect-[4/5] w-full object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-ink">🏡 {{ $item['title'] }}</h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white px-5 py-12">
        <div class="mx-auto max-w-6xl">
            <div class="mb-7 flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-pine-500">CUSTOMER STORIES</p>
                    <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">รีวิวลูกค้า</h2>
                </div>
                <a href="{{ route('reviews.index') }}" class="hidden rounded-full border border-pine-200 px-5 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50 sm:inline-flex">อ่านรีวิว</a>
            </div>
            <div class="grid gap-5 md:grid-cols-2">
                @forelse (($customerReviews ?? collect())->take(2) as $review)
                    <article class="overflow-hidden rounded-[24px] bg-[#fffaf3] shadow-[0_18px_42px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                        <div class="grid md:grid-cols-[.95fr_1.05fr]">
                            <div class="relative aspect-[4/3] md:aspect-auto">
                                @if ($review->image_path)
                                    <img src="{{ asset('storage/'.$review->image_path) }}" alt="ผลงานของ {{ $review->customer_name }}" class="h-full w-full object-cover">
                                @else
                                    <img src="https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85" alt="ผลงาน {{ company()->display_name }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="p-6">
                                <p class="text-lg text-amber-500">{{ str_repeat('★', $review->rating) }}</p>
                                <p class="mt-4 text-xl leading-8 text-ink">"{{ $review->review_text }}"</p>
                                <div class="mt-6 flex items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80" alt="คุณ{{ $review->customer_name }}" class="h-12 w-12 rounded-full object-cover">
                                    <div>
                                        <p class="font-semibold text-ink">คุณ{{ $review->customer_name }}</p>
                                        <p class="text-sm text-pine-700">{{ $review->province ? 'จังหวัด'.$review->province : 'ลูกค้า '.$company->display_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    @foreach ($reviewFallbacks as $review)
                        <article class="overflow-hidden rounded-[24px] bg-[#fffaf3] shadow-[0_18px_42px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                            <div class="grid md:grid-cols-[.95fr_1.05fr]">
                                <img src="{{ $review['project'] }}" alt="ผลงานของคุณ{{ $review['name'] }}" class="aspect-[4/3] h-full w-full object-cover">
                                <div class="p-6">
                                    <p class="text-lg text-amber-500">{{ str_repeat('★', $review['rating']) }}</p>
                                    <p class="mt-4 text-xl leading-8 text-ink">"{{ $review['text'] }}"</p>
                                    <div class="mt-6 flex items-center gap-3">
                                        <img src="{{ $review['photo'] }}" alt="คุณ{{ $review['name'] }}" class="h-12 w-12 rounded-full object-cover">
                                        <div>
                                            <p class="font-semibold text-ink">คุณ{{ $review['name'] }}</p>
                                            <p class="text-sm text-pine-700">จังหวัด{{ $review['province'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-[#fffaf3] px-5 py-12">
        <div class="mx-auto max-w-6xl">
            <div class="mb-8 max-w-2xl">
                <p class="text-sm font-semibold text-pine-500">PROCESS</p>
                <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">จากพื้นที่จริงสู่งานติดตั้ง</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                @foreach ([
                    ['no' => '1', 'title' => 'วัดพื้นที่', 'text' => 'ดูขนาดและข้อจำกัดหน้างาน'],
                    ['no' => '2', 'title' => 'ออกแบบ', 'text' => 'จัดฟังก์ชันให้เข้ากับชีวิตจริง'],
                    ['no' => '3', 'title' => 'ผลิต', 'text' => 'งานไม้สนโดยทีมช่างของเรา'],
                    ['no' => '4', 'title' => 'ติดตั้ง', 'text' => 'ส่งมอบพร้อมตรวจรายละเอียด'],
                ] as $step)
                    <article class="rounded-[22px] bg-white p-6 shadow-[0_18px_42px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-pine-700 text-lg font-semibold text-white">{{ $step['no'] }}</div>
                        <h3 class="mt-5 text-xl font-semibold text-ink">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-pine-700">{{ $step['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="px-5 py-12">
        <div class="mx-auto max-w-6xl overflow-hidden rounded-[24px] bg-pine-700 shadow-[0_24px_70px_rgba(93,66,39,.22)] md:grid md:grid-cols-[1.1fr_.9fr]">
            <div class="p-8 text-white md:p-12">
                <p class="text-sm font-semibold text-pine-100">START YOUR CUSTOM PROJECT</p>
                <h2 class="mt-3 text-4xl font-semibold leading-tight md:text-6xl">เริ่มต้นสร้างบ้านในฝันของคุณ</h2>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('lead.create') }}" class="inline-flex min-h-12 items-center justify-center rounded-full bg-white px-6 py-3 font-semibold text-pine-700 hover:bg-pine-50">ขอแบบและประเมินฟรี</a>
                    @if ($company->line_oa_url)
                        <a href="{{ $company->line_oa_url }}" class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/60 px-6 py-3 font-semibold text-white hover:bg-white/10">ติดต่อผ่าน LINE</a>
                    @endif
                </div>
            </div>
            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=85" alt="บ้านอบอุ่นพร้อมเฟอร์นิเจอร์ไม้สน" class="h-full min-h-72 w-full object-cover">
        </div>
    </section>

    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-pine-100 bg-white/95 p-3 shadow-[0_-10px_30px_rgba(93,66,39,.12)] backdrop-blur md:hidden">
        <div class="mx-auto flex max-w-[420px] gap-2">
            <a href="{{ route('portfolio.index') }}" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-pine-50 px-4 text-sm font-semibold text-pine-700">ดูผลงาน</a>
            <a href="{{ route('lead.create') }}" class="inline-flex min-h-12 flex-[1.35] items-center justify-center rounded-full bg-pine-700 px-4 text-sm font-semibold text-white">ขอแบบฟรี</a>
        </div>
    </div>
</div>
@endsection
