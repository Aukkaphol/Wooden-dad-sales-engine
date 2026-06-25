@extends('layouts.public', ['title' => company()->display_name.' | '.__('messages.hero.title_line_1')])

@php
    use Illuminate\Support\Facades\Storage;

    $company = company();
    $locale = app()->getLocale();
    $hero = $sections['hero'] ?? null;
    $fallbackHeroPath = 'homepage/Wjsv8qpm3ewcr77GnofS0Xy0zI7KvxwJPC4mzWFQ.jpg';
    $fallbackAltHeroPath = 'homepage/jyrvH2AKrAUjKyyMPyoIDQAOfhTRp9o3uE3YVL40.jpg';
    $heroPath = $hero?->image_path;

    if (! $heroPath || ! Storage::disk('public')->exists($heroPath)) {
        $heroPath = Storage::disk('public')->exists($fallbackHeroPath)
            ? $fallbackHeroPath
            : (Storage::disk('public')->exists($fallbackAltHeroPath) ? $fallbackAltHeroPath : null);
    }

    $heroImage = $heroPath
        ? asset('storage/'.$heroPath)
        : 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1800&q=88';
    $heroImageCss = "background-image: url('".$heroImage."'), linear-gradient(135deg, #6b5039 0%, #d9c4a6 100%);";

    $portfolioItems = ($latestPortfolioImages ?? collect())->take(4);
    $reviewItems = ($customerReviews ?? collect())->take(4);
    $portfolioFallbacks = collect([
        ['image' => $heroImage, 'title' => __('messages.nav.bedroom_sets'), 'count' => '120+'],
        ['image' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=900&q=85', 'title' => __('messages.nav.portfolio'), 'count' => '86+'],
        ['image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=85', 'title' => __('messages.sections.room_inspiration'), 'count' => '64+'],
        ['image' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=900&q=85', 'title' => __('messages.sections.show_work'), 'count' => '48+'],
    ]);
    $trustItems = [
        ['title' => __('messages.trust.measure_title'), 'text' => __('messages.trust.measure_text')],
        ['title' => __('messages.trust.price_title'), 'text' => __('messages.trust.price_text')],
        ['title' => __('messages.trust.pine_title'), 'text' => __('messages.trust.pine_text')],
        ['title' => __('messages.trust.warranty_title'), 'text' => __('messages.trust.warranty_text')],
    ];
@endphp

@section('content')
    <div class="wd-home bg-[#fffaf3] pb-24 text-ink md:pb-0">
        <section class="relative isolate min-h-svh overflow-hidden bg-[#221810]">
            <div class="wd-hero-image absolute inset-0" style="{{ $heroImageCss }}"></div>
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(31,22,15,.74)_0%,rgba(31,22,15,.50)_43%,rgba(31,22,15,.12)_100%)]"></div>
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(31,22,15,.24)_0%,rgba(86,55,28,.10)_48%,rgba(31,22,15,.58)_100%)]"></div>
            <div class="absolute inset-0 bg-[linear-gradient(115deg,rgba(139,90,43,.18)_0%,rgba(247,214,167,.08)_46%,rgba(63,40,24,.12)_100%)]"></div>
            <div class="wd-hero-grain absolute inset-0"></div>

            <div class="relative mx-auto flex min-h-svh max-w-7xl flex-col justify-end px-5 pb-8 pt-32 sm:px-6 md:pb-12 lg:px-8">
                <div class="grid items-end gap-8 lg:grid-cols-[minmax(0,1fr)_420px] xl:grid-cols-[minmax(0,1fr)_460px]">
                    <div class="max-w-[760px] pb-2 md:pb-10">
                        <div class="mb-5 inline-flex rounded-full border border-white/25 bg-white/15 px-4 py-2 text-xs font-semibold text-white shadow-sm backdrop-blur-md">
                            {{ __('messages.hero.badge') }}
                        </div>
                        <h1 class="text-[2.32rem] font-semibold leading-[1.11] tracking-normal text-white drop-shadow-[0_8px_28px_rgba(0,0,0,.32)] sm:text-[3.8rem] sm:leading-[1.08] lg:text-[4.18rem] lg:leading-[1.1]">
                            {{ __('messages.hero.title_line_1') }}<br>
                            <span class="text-white/95">{{ __('messages.hero.title_line_2') }}</span>
                        </h1>
                        <p class="mt-7 max-w-2xl text-base font-medium leading-8 text-white/90 sm:text-lg md:text-xl md:leading-9">{{ __('messages.hero.subtitle') }}</p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('lead.create', ['locale' => $locale]) }}" class="inline-flex min-h-13 items-center justify-center rounded-full bg-white px-7 py-3.5 text-base font-semibold text-pine-800 shadow-[0_20px_55px_rgba(0,0,0,.20)] transition hover:bg-pine-50">{{ __('messages.hero.primary_cta') }}</a>
                            <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" class="inline-flex min-h-13 items-center justify-center rounded-full border border-white/70 bg-white/10 px-7 py-3.5 text-base font-semibold text-white shadow-sm backdrop-blur-md transition hover:bg-white/18">{{ __('messages.hero.secondary_cta') }}</a>
                        </div>
                    </div>

                    <aside class="wd-hero-panel hidden overflow-hidden rounded-[32px] border border-white/28 bg-white/16 p-3 shadow-[0_24px_68px_rgba(20,13,8,.20)] backdrop-blur-xl lg:block">
                        <div class="wd-showcase-image min-h-[420px] rounded-[26px]" style="{{ $heroImageCss }}" role="img" aria-label="{{ __('messages.hero.title_line_1') }}"></div>
                        <div class="grid grid-cols-2 gap-2 px-2 py-3">
                            @foreach ([__('messages.hero.custom'), __('messages.hero.pine'), __('messages.hero.install'), __('messages.trust.measure_title')] as $chip)
                                <div class="rounded-2xl border border-white/22 bg-white/18 px-3 py-2.5 text-sm font-semibold leading-5 text-white shadow-sm backdrop-blur-md">
                                    {{ $chip }}
                                </div>
                            @endforeach
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="-mt-1 rounded-t-[30px] bg-[#F5F0E8]/95 px-5 py-6 shadow-[0_-24px_70px_rgba(43,31,22,.08)] ring-1 ring-white/75 backdrop-blur">
            <div class="mx-auto grid max-w-6xl gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($trustItems as $trustItem)
                    <article class="rounded-[24px] border border-white/70 bg-white/56 p-5 shadow-[0_18px_45px_rgba(93,66,39,.07)] backdrop-blur">
                        <h2 class="text-base font-semibold text-ink">{{ $trustItem['title'] }}</h2>
                        <p class="mt-2 text-sm leading-6 text-pine-700">{{ $trustItem['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bg-[#fffaf3] px-5 py-14 md:py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <h2 class="max-w-3xl text-3xl font-semibold leading-tight text-ink md:text-5xl">{{ __('messages.sections.show_work') }}</h2>
                    <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" class="hidden rounded-full border border-pine-200 bg-white/70 px-5 py-2.5 text-sm font-semibold text-pine-700 shadow-sm transition hover:bg-white sm:inline-flex">{{ __('messages.sections.show_all') }}</a>
                </div>

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    @forelse ($portfolioItems as $item)
                        <article class="wd-card group overflow-hidden rounded-[26px] border border-pine-100 bg-white shadow-[0_20px_50px_rgba(93,66,39,.10)]">
                            <div class="relative aspect-[4/5] overflow-hidden">
                                <img src="{{ asset('storage/'.$item->image_path) }}" alt="{{ $item->title ?: $item->category_name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]">
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/68 via-black/20 to-transparent p-5 text-white">
                                    <h3 class="text-xl font-semibold">{{ $item->category_name ?: __('messages.sections.show_work') }}</h3>
                                    <p class="mt-1 text-sm text-white/82">{{ $item->title ?: company()->display_name }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        @foreach ($portfolioFallbacks as $item)
                            <article class="wd-card group overflow-hidden rounded-[26px] border border-pine-100 bg-white shadow-[0_20px_50px_rgba(93,66,39,.10)]">
                                <div class="relative aspect-[4/5] overflow-hidden">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.035]">
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/68 via-black/20 to-transparent p-5 text-white">
                                        <h3 class="text-xl font-semibold">{{ $item['title'] }}</h3>
                                        <p class="mt-1 text-sm text-white/82">{{ $item['count'] }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-white px-5 py-14 md:py-20">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <h2 class="max-w-3xl text-3xl font-semibold leading-tight text-ink md:text-5xl">{{ __('messages.sections.customer_reviews') }}</h2>
                    <a href="{{ route('reviews.index', ['locale' => $locale]) }}" class="hidden rounded-full border border-pine-200 px-5 py-2.5 text-sm font-semibold text-pine-700 transition hover:bg-pine-50 sm:inline-flex">{{ __('messages.sections.read_reviews') }}</a>
                </div>

                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    @forelse ($reviewItems as $review)
                        <article class="wd-card rounded-[26px] border border-pine-100 bg-[#fffaf3] p-4 shadow-[0_18px_42px_rgba(93,66,39,.08)]">
                            <div class="aspect-[4/3] overflow-hidden rounded-[20px] bg-pine-100">
                                <img src="{{ $review->image_path ? asset('storage/'.$review->image_path) : $heroImage }}" alt="{{ $review->customer_name }}" class="h-full w-full object-cover">
                            </div>
                            <p class="mt-5 text-sm text-amber-500">{{ str_repeat('★', $review->rating) }}</p>
                            <p class="mt-3 text-base leading-7 text-ink">"{{ $review->review_text }}"</p>
                            <p class="mt-5 font-semibold text-ink">{{ $review->customer_name }}</p>
                            <p class="text-sm text-pine-700">{{ $review->province ?: __('messages.sections.customer_reviews') }}</p>
                        </article>
                    @empty
                        @foreach (range(1, 4) as $index)
                            <article class="wd-card rounded-[26px] border border-pine-100 bg-[#fffaf3] p-4 shadow-[0_18px_42px_rgba(93,66,39,.08)]">
                                <div class="aspect-[4/3] overflow-hidden rounded-[20px] bg-pine-100">
                                    <img src="{{ $index % 2 ? $heroImage : 'https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=900&q=85' }}" alt="{{ company()->display_name }}" class="h-full w-full object-cover">
                                </div>
                                <p class="mt-5 text-sm text-amber-500">★★★★★</p>
                                <p class="mt-3 text-base leading-7 text-ink">"{{ __('messages.footer.description') }}"</p>
                                <p class="mt-5 font-semibold text-ink">{{ company()->display_name }}</p>
                                <p class="text-sm text-pine-700">{{ __('messages.sections.customer_reviews') }}</p>
                            </article>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-[#fffaf3] px-5 py-14 md:py-20">
            <div class="mx-auto max-w-6xl overflow-hidden rounded-[30px] bg-[#5d4227] shadow-[0_24px_70px_rgba(93,66,39,.22)] md:grid md:grid-cols-[1.08fr_.92fr]">
                <div class="p-8 text-white md:p-12">
                    <h2 class="max-w-2xl text-4xl font-semibold leading-tight md:text-6xl">{{ __('messages.sections.final_cta') }}</h2>
                    <p class="mt-5 max-w-xl text-base leading-8 text-white/82 md:text-lg">{{ __('messages.footer.description') }}</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('lead.create', ['locale' => $locale]) }}" class="inline-flex min-h-12 items-center justify-center rounded-full bg-white px-6 py-3 font-semibold text-pine-700 shadow-[0_16px_36px_rgba(0,0,0,.16)] transition hover:bg-pine-50">{{ __('messages.hero.primary_cta') }}</a>
                        @if ($company->line_oa_url)
                            <a href="{{ $company->line_oa_url }}" class="inline-flex min-h-12 items-center justify-center rounded-full border border-white/60 px-6 py-3 font-semibold text-white transition hover:bg-white/10">{{ __('messages.sections.line_contact') }}</a>
                        @endif
                    </div>
                </div>
                <div class="min-h-72 bg-cover bg-center" style="{{ $heroImageCss }}" aria-hidden="true"></div>
            </div>
        </section>

        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-pine-100 bg-white/95 p-3 shadow-[0_-10px_30px_rgba(93,66,39,.12)] backdrop-blur md:hidden">
            <div class="mx-auto flex max-w-[420px] gap-2">
                <a href="{{ route('portfolio.index', ['locale' => $locale]) }}" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-pine-50 px-4 text-sm font-semibold text-pine-700">{{ __('messages.hero.secondary_cta') }}</a>
                <a href="{{ route('lead.create', ['locale' => $locale]) }}" class="inline-flex min-h-12 flex-[1.35] items-center justify-center rounded-full bg-pine-700 px-4 text-sm font-semibold text-white">{{ __('messages.hero.primary_cta') }}</a>
            </div>
        </div>
    </div>
@endsection
