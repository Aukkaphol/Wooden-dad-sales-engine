@extends('layouts.public', ['title' => __('messages.nav.portfolio').' | '.company()->display_name])

@php($en = app()->getLocale() === 'en')

@section('content')
<section class="bg-[linear-gradient(120deg,#fbf7ef_0%,#ffffff_55%,#f0dfc3_100%)]">
    <div class="mx-auto max-w-6xl px-5 py-14">
        <p class="text-sm font-semibold text-pine-500">Portfolio Gallery</p>
        <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-semibold text-ink md:text-5xl">{{ $en ? 'Custom pine wood furniture portfolio' : 'ผลงานเฟอร์นิเจอร์ไม้สนสั่งทำ' }}</h1>
                <p class="mt-4 max-w-2xl leading-8 text-pine-700">{{ $en ? 'Real projects from '.company()->display_name.' across bedrooms, living rooms, dining rooms, and workspaces.' : 'รวมตัวอย่างงานจริงจาก '.company()->display_name.' ทั้งห้องนอน ห้องนั่งเล่น ห้องอาหาร และห้องทำงาน' }}</p>
            </div>
            <a href="{{ route('lead.create') }}" class="inline-flex w-fit items-center justify-center rounded-full bg-pine-700 px-6 py-3 text-sm font-semibold text-white hover:bg-pine-500">{{ __('messages.hero.cta_primary') }}</a>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-6xl px-5 py-12">
        <div class="mb-8 flex flex-wrap gap-2">
            <a href="{{ route('portfolio.index') }}" class="rounded-full bg-pine-700 px-4 py-2 text-sm font-semibold text-white">{{ $en ? 'All' : 'ทั้งหมด' }}</a>
            @foreach ($categories as $key => $label)
                <a href="#{{ $key }}" class="rounded-full bg-pine-50 px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">{{ $label }}</a>
            @endforeach
        </div>

        @if ($portfolioImages->isNotEmpty())
            <div class="columns-1 gap-5 sm:columns-2 lg:columns-3">
                @foreach ($portfolioImages as $image)
                    <article id="{{ $image->category }}" class="mb-5 break-inside-avoid overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-pine-200">
                        <button type="button" class="group block w-full text-left" data-gallery-open="{{ asset('storage/'.$image->image_path) }}" data-gallery-title="{{ $image->title ?: $image->category_name }}">
                            <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $image->title ?: $image->category_name }}" class="w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                            <span class="block p-4">
                                <span class="block text-xs font-semibold text-pine-500">{{ $image->category_name }}</span>
                                <span class="mt-1 block font-semibold text-ink">{{ $image->title ?: ($en ? 'Project '.$image->category_name : 'ผลงาน '.$image->category_name) }}</span>
                            </span>
                        </button>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-lg border border-dashed border-pine-300 bg-pine-50 p-10 text-center">
                <h2 class="text-xl font-semibold text-ink">{{ $en ? 'No portfolio images yet' : 'ยังไม่มีรูปผลงาน' }}</h2>
                <p class="mt-2 text-pine-700">{{ $en ? 'When admins add portfolio images, they will appear in this gallery automatically.' : 'เมื่อแอดมินเพิ่มรูปผลงาน รูปจะแสดงใน Gallery นี้โดยอัตโนมัติ' }}</p>
            </div>
        @endif
    </div>
</section>

<dialog id="galleryDialog" class="w-[min(920px,92vw)] rounded-lg bg-transparent p-0 backdrop:bg-black/75">
    <div class="overflow-hidden rounded-lg bg-white shadow-xl">
        <div class="flex items-center justify-between gap-4 border-b border-pine-100 px-4 py-3">
            <p id="galleryDialogTitle" class="font-semibold text-ink">{{ __('messages.nav.portfolio') }}</p>
            <button type="button" class="rounded-full bg-pine-50 px-3 py-1.5 text-sm font-semibold text-pine-700 hover:bg-pine-100" data-gallery-close>{{ $en ? 'Close' : 'ปิด' }}</button>
        </div>
        <img id="galleryDialogImage" src="" alt="" class="max-h-[78vh] w-full bg-pine-50 object-contain">
    </div>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dialog = document.getElementById('galleryDialog');
        const image = document.getElementById('galleryDialogImage');
        const title = document.getElementById('galleryDialogTitle');

        document.querySelectorAll('[data-gallery-open]').forEach((button) => {
            button.addEventListener('click', () => {
                image.src = button.dataset.galleryOpen;
                image.alt = button.dataset.galleryTitle || '{{ __('messages.nav.portfolio') }}';
                title.textContent = button.dataset.galleryTitle || '{{ __('messages.nav.portfolio') }}';
                dialog.showModal();
            });
        });

        document.querySelector('[data-gallery-close]')?.addEventListener('click', () => dialog.close());
        dialog?.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });
</script>
@endsection
