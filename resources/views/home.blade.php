@extends('layouts.public', ['title' => company()->display_name.' | '.__('messages.hero.title_line_1')])

@php
    $company = company();
    $locale = app()->getLocale();
    $hero = $sections['hero'] ?? null;
    $heroImage = $hero?->image_path ? asset('storage/'.$hero->image_path) : 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1600&q=85';
    $portfolioItems = ($latestPortfolioImages ?? collect())->take(6);
    $reviewItems = ($customerReviews ?? collect())->take(2);
    $portfolioFallbacks = collect($locale === 'en' ? [
        ['image' => 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85', 'name' => 'Somchai Residence', 'room' => 'Bedroom', 'province' => 'Kamphaeng Phet'],
        ['image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85', 'name' => 'Or Residence', 'room' => 'Living Room', 'province' => 'Chiang Mai'],
        ['image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=85', 'name' => 'Kitti Residence', 'room' => 'Home Office', 'province' => 'Nakhon Sawan'],
    ] : [
        ['image' => 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85', 'name' => 'บ้านคุณสมชาย', 'room' => 'ห้องนอน', 'province' => 'กำแพงเพชร'],
        ['image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85', 'name' => 'บ้านคุณอร', 'room' => 'ห้องรับแขก', 'province' => 'เชียงใหม่'],
        ['image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=85', 'name' => 'บ้านคุณกิตติ', 'room' => 'ห้องทำงาน', 'province' => 'นครสวรรค์'],
    ]);
    $inspirations = $locale === 'en' ? [
        ['title' => 'Minimal Home', 'image' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'Japanese Home', 'image' => 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'Modern Home', 'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'Pine Wood Cafe', 'image' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'Home Office', 'image' => 'https://images.unsplash.com/photo-1600494448850-6013c64ba722?auto=format&fit=crop&w=900&q=85'],
    ] : [
        ['title' => 'บ้านมินิมอล', 'image' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'บ้านญี่ปุ่น', 'image' => 'https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'บ้านโมเดิร์น', 'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'คาเฟ่ไม้สน', 'image' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=900&q=85'],
        ['title' => 'ห้องทำงาน', 'image' => 'https://images.unsplash.com/photo-1600494448850-6013c64ba722?auto=format&fit=crop&w=900&q=85'],
    ];
    $processSteps = $locale === 'en'
        ? ['Measure', 'Design', 'Craft', 'Install']
        : ['วัดพื้นที่', 'ออกแบบ', 'ผลิต', 'ติดตั้ง'];
@endphp

@section('content')
    <div class="bg-[#fffaf3] pb-24 text-ink md:pb-0">
        <section class="relative min-h-svh overflow-hidden bg-[#1d1711]">
            <img src="{{ $heroImage }}" alt="{{ company()->display_name }}" class="absolute inset-0 h-full w-full scale-[1.04] object-cover saturate-105">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(18,14,10,.78)_0%,rgba(18,14,10,.46)_44%,rgba(18,14,10,.20)_100%)]"></div>
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(18,14,10,.56)_0%,rgba(18,14,10,.18)_40%,rgba(18,14,10,.78)_100%)]"></div>

            <div class="relative mx-auto flex min-h-svh max-w-7xl flex-col justify-end px-5 pb-12 pt-36 sm:px-6 md:pb-20 lg:px-8">
                <div class="max-w-[420px] md:max-w-3xl">
                    <div class="mb-5 inline-flex rounded-full border border-white/25 bg-white/16 px-4 py-2 text-xs font-semibold text-white shadow-sm backdrop-blur-md">
                        {{ __('messages.hero.badge') }}
                    </div>
                    <h1 class="text-[2.18rem] font-semibold leading-[1.12] tracking-normal text-white drop-shadow-[0_8px_28px_rgba(0,0,0,.32)] sm:text-5xl md:text-[3.45rem] lg:text-[3.8rem]">
                        {{ __('messages.hero.title_line_1') }}<br>
                        <span class="text-white/95">{{ __('messages.hero.title_line_2') }}</span>
                    </h1>
                    <p class="mt-8 max-w-2xl text-lg font-medium leading-8 text-white/90 md:text-xl md:leading-9">{{ __('messages.hero.subtitle') }}</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('lead.create', ['locale' => app()->getLocale()]) }}"<a href="{{ route('lead.create', ['locale' => app()->getLocale()]) }}" class="inline-flex min-h-13 items-center justify-center rounded-full bg-white px-7 py-3.5 text-base font-semibold text-pine-800 shadow-[0_20px_55px_rgba(0,0,0,.20)] transition hover:bg-pine-50">{{ __('messages.hero.primary_cta') }}</a>
                        <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" class="inline-flex min-h-13 items-center justify-center rounded-full border border-white/70 bg-white/10 px-7 py-3.5 text-base font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/18">{{ __('messages.hero.secondary_cta') }}</a>
                    </div>
                    <div class="mt-8 grid max-w-xl grid-cols-3 gap-3 text-white">
                        @foreach ([['Custom', __('messages.hero.custom')], ['Pine', __('messages.hero.pine')], ['Install', __('messages.hero.install')]] as $item)
                            <div class="rounded-2xl border border-white/18 bg-white/12 p-3 backdrop-blur-md">
                                <p class="text-lg font-semibold">{{ $item[0] }}</p>
                                <p class="mt-1 text-xs leading-5 text-white/78">{{ $item[1] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#fffaf3] px-5 py-8">
            <div class="mx-auto grid max-w-6xl gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['title' => __('messages.trust.measure_title'), 'text' => __('messages.trust.measure_text')],
                    ['title' => __('messages.trust.price_title'), 'text' => __('messages.trust.price_text')],
                    ['title' => __('messages.trust.pine_title'), 'text' => __('messages.trust.pine_text')],
                    ['title' => __('messages.trust.warranty_title'), 'text' => __('messages.trust.warranty_text')],
                ] as $trustItem)
                    <article class="rounded-[22px] bg-white p-5 shadow-[0_18px_45px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                        <h2 class="text-lg font-semibold text-ink">{{ $trustItem['title'] }}</h2>
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
                        <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">{{ __('messages.sections.show_work') }}</h2>
                    </div>
                    <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" class="hidden rounded-full border border-pine-200 px-5 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50 sm:inline-flex">{{ __('messages.sections.show_all') }}</a>
                </div>

                <div class="grid gap-5 lg:grid-cols-3">
                    @forelse ($portfolioItems as $index => $item)
                        <article class="{{ $index === 0 ? 'lg:col-span-2 lg:row-span-2' : '' }} group overflow-hidden rounded-[24px] bg-pine-50 shadow-[0_20px_50px_rgba(93,66,39,.10)]">
                            <div class="{{ $index === 0 ? 'aspect-[4/5] md:aspect-[16/11]' : 'aspect-[4/5]' }} relative">
                                <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title ?: $item->category_name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-5 text-white">
                                    <h3 class="text-2xl font-semibold">{{ $item->title ?: company()->display_name }}</h3>
                                    <p class="mt-1 text-sm text-white/85">{{ $item->category_name }}</p>
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
                                        <p class="mt-1 text-sm text-white/85">{{ $item['room'] }} / {{ $item['province'] }}</p>
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
                    <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">{{ __('messages.sections.room_inspiration') }}</h2>
                </div>
                <div class="flex snap-x gap-4 overflow-x-auto pb-3 lg:grid lg:grid-cols-5 lg:overflow-visible">
                    @foreach ($inspirations as $item)
                        <article class="min-w-[78%] snap-start overflow-hidden rounded-[24px] bg-white shadow-[0_18px_42px_rgba(93,66,39,.08)] sm:min-w-[42%] lg:min-w-0">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="aspect-[4/5] w-full object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-ink">{{ $item['title'] }}</h3>
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
                        <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">{{ __('messages.sections.customer_reviews') }}</h2>
                    </div>
                    <a href="{{ route('reviews.index', ['locale' => app()->getLocale()]) }}" class="hidden rounded-full border border-pine-200 px-5 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50 sm:inline-flex">{{ __('messages.sections.read_reviews') }}</a>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    @forelse ($reviewItems as $review)
                        <article class="overflow-hidden rounded-[24px] bg-[#fffaf3] shadow-[0_18px_42px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                            <div class="grid md:grid-cols-[.95fr_1.05fr]">
                                <img src="{{ $review->image_path ? asset('storage/'.$review->image_path) : 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85' }}" alt="{{ $review->customer_name }}" class="aspect-[4/3] h-full w-full object-cover">
                                <div class="p-6">
                                    <p class="text-lg text-amber-500">{{ str_repeat('★', $review->rating) }}</p>
                                    <p class="mt-4 text-xl leading-8 text-ink">"{{ $review->review_text }}"</p>
                                    <p class="mt-6 font-semibold text-ink">{{ $locale === 'en' ? 'Khun ' : 'คุณ' }}{{ $review->customer_name }}</p>
                                    <p class="text-sm text-pine-700">{{ $review->province }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="rounded-[24px] bg-[#fffaf3] p-6 shadow-[0_18px_42px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                            <p class="text-lg text-amber-500">★★★★★</p>
                            <p class="mt-4 text-xl leading-8 text-ink">"{{ $locale === 'en' ? 'Beautiful craftsmanship and delivered on time.' : 'งานสวยมาก ส่งตรงเวลา' }}"</p>
                            <p class="mt-6 font-semibold text-ink">{{ $locale === 'en' ? 'Khun Somchai' : 'คุณสมชาย' }}</p>
                            <p class="text-sm text-pine-700">{{ $locale === 'en' ? 'Chiang Mai' : 'จังหวัดเชียงใหม่' }}</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-[#fffaf3] px-5 py-12">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 max-w-2xl">
                    <p class="text-sm font-semibold text-pine-500">PROCESS</p>
                    <h2 class="mt-2 text-3xl font-semibold text-ink md:text-5xl">{{ __('messages.sections.process') }}</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-4">
                    @foreach ($processSteps as $index => $step)
                        <article class="rounded-[22px] bg-white p-6 shadow-[0_18px_42px_rgba(93,66,39,.08)] ring-1 ring-pine-100">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-pine-700 text-lg font-semibold text-white">{{ $index + 1 }}</div>
                            <h3 class="mt-5 text-xl font-semibold text-ink">{{ $step }}</h3>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="px-5 py-12">
            <div class="mx-auto max-w-6xl overflow-hidden rounded-[24px] bg-pine-700 shadow-[0_24px_70px_rgba(93,66,39,.22)] md:grid md:grid-cols-[1.1fr_.9fr]">
                <div class="p-8 text-white md:p-12">
                    <p class="text-sm font-semibold text-pine-100">START YOUR CUSTOM PROJECT</p>
                    <h2 class="mt-3 text-4xl font-semibold leading-tight md:text-6xl">{{ __('messages.sections.final_cta') }}</h2>
                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('lead.create', ['locale' => app()->getLocale()]) }}" class="inline-flex min-h-12 items-center justify-center rounded-full bg-white px-6 py-3 font-semibold text-pine-700 hover:bg-pine-50">{{ __('messages.hero.primary_cta') }}</a>
                        @if ($company->line_oa_url)
                            <a href="{{ $company->line_oa_url }}" class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/60 px-6 py-3 font-semibold text-white hover:bg-white/10">{{ __('messages.sections.line_contact') }}</a>
                        @endif
                    </div>
                </div>
                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=85" alt="{{ company()->display_name }}" class="h-full min-h-72 w-full object-cover">
            </div>
        </section>

        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-pine-100 bg-white/95 p-3 shadow-[0_-10px_30px_rgba(93,66,39,.12)] backdrop-blur md:hidden">
            <div class="mx-auto flex max-w-[420px] gap-2">
                <a href="{{ route('portfolio.index', ['locale' => app()->getLocale()]) }}" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-pine-50 px-4 text-sm font-semibold text-pine-700">{{ __('messages.hero.secondary_cta') }}</a>
                <a href="{{ route('lead.create', ['locale' => app()->getLocale()]) }}" class="inline-flex min-h-12 flex-[1.35] items-center justify-center rounded-full bg-pine-700 px-4 text-sm font-semibold text-white">{{ __('messages.hero.primary_cta') }}</a>
            </div>
        </div>
    </div>
@endsection
