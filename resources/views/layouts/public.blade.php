<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $company = company();
    @endphp
    <title>{{ $title ?? $company->display_name }}</title>
    @if ($company->favicon_url)
        <link rel="icon" href="{{ $company->favicon_url }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-thai:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fffaf3] text-ink antialiased">
    @php
        $publicMenu = [
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Portfolio', 'route' => 'portfolio.index'],
            ['label' => 'Bedroom Sets', 'route' => 'bedroom-set'],
            ['label' => 'Reviews', 'route' => 'reviews.index'],
            ['label' => 'Contact Us', 'route' => 'lead.create'],
        ];
    @endphp

    <header class="fixed inset-x-0 top-0 z-50 px-4 pt-4">
        <nav class="mx-auto max-w-7xl rounded-[28px] border border-white/60 bg-white/78 px-4 py-3 shadow-[0_18px_55px_rgba(93,66,39,.14)] backdrop-blur-2xl md:px-5">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3" aria-label="{{ $company->display_name }}">
                    @if ($company->logo_url)
                        <img src="{{ $company->logo_url }}" alt="{{ $company->display_name }}" class="h-10 w-10 shrink-0 rounded-full bg-white object-contain shadow-[0_10px_24px_rgba(93,66,39,.20)]">
                    @else
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-pine-700 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(93,66,39,.20)]">WD</span>
                    @endif
                    <span class="min-w-0">
                        <span class="block truncate text-base font-semibold leading-tight text-[#2b2118]">{{ $company->display_name }}</span>
                        <span class="block truncate text-xs font-medium text-pine-700">Custom Pine Wood Furniture</span>
                    </span>
                </a>

                <div class="hidden items-center gap-7 text-sm font-semibold text-[#4f3f2f] lg:flex">
                    @foreach ($publicMenu as $item)
                        <a href="{{ route($item['route']) }}" class="hover:text-pine-700">{{ $item['label'] }}</a>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('lead.create') }}" class="hidden min-h-11 items-center justify-center rounded-full bg-pine-700 px-5 text-sm font-semibold text-white shadow-[0_12px_30px_rgba(93,66,39,.22)] transition hover:bg-pine-500 sm:inline-flex">Request Quotation</a>
                    <details class="group relative lg:hidden">
                        <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full border border-pine-200 bg-white text-pine-800 shadow-sm marker:hidden" aria-label="Open menu">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-open:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5 group-open:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </summary>
                        <div class="absolute right-0 top-14 w-[min(82vw,320px)] rounded-[22px] border border-pine-100 bg-white p-3 text-base font-semibold text-[#4f3f2f] shadow-[0_24px_70px_rgba(28,23,18,.20)]">
                            @foreach ($publicMenu as $item)
                                <a href="{{ route($item['route']) }}" class="block rounded-2xl px-3 py-3 hover:bg-pine-50">{{ $item['label'] }}</a>
                            @endforeach
                            <a href="{{ route('lead.create') }}" class="mt-2 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-pine-700 px-5 text-center text-sm font-semibold text-white shadow-sm">Request Quotation</a>
                        </div>
                    </details>
                </div>
            </div>
        </nav>
    </header>

    <main class="{{ request()->routeIs('home') ? '' : 'pt-24' }}">
        @yield('content')
    </main>

    @if ($company->line_oa_url)
        <a href="{{ $company->line_oa_url }}" class="fixed bottom-24 right-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#06C755] text-xl font-bold text-white shadow-lg shadow-black/20 md:bottom-6" aria-label="Contact via LINE">LINE</a>
    @endif

    <footer class="border-t border-pine-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-6 px-5 py-10 text-sm text-pine-700 md:grid-cols-[1.1fr_.9fr_.9fr]">
            <div>
                <p class="text-lg font-semibold text-ink">{{ $company->display_name }}</p>
                <p class="mt-2 leading-7">เฟอร์นิเจอร์ไม้สนสั่งทำ ออกแบบตามพื้นที่จริง ผลิตและติดตั้งโดยทีมงานมืออาชีพ</p>
                @if ($company->address)
                    <p class="mt-2 whitespace-pre-line leading-7">{{ $company->address }}</p>
                @endif
            </div>
            <div>
                <p class="font-semibold text-ink">Contact</p>
                @if ($company->phone)
                    <p class="mt-2">Tel {{ $company->phone }}</p>
                @endif
                @if ($company->email)
                    <p>Email {{ $company->email }}</p>
                @endif
                @if ($company->line_oa_id)
                    <p>LINE {{ $company->line_oa_id }}</p>
                @endif
                @if ($company->facebook_url)
                    <a href="{{ $company->facebook_url }}" class="mt-1 block hover:text-ink">Facebook</a>
                @endif
                @if ($company->website_url)
                    <a href="{{ $company->website_url }}" class="mt-1 block hover:text-ink">{{ $company->website_display }}</a>
                @endif
            </div>
            <div>
                <p class="font-semibold text-ink">Menu</p>
                <div class="mt-2 grid gap-1">
                    @foreach ($publicMenu as $item)
                        <a href="{{ route($item['route']) }}" class="hover:text-ink">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ route('lead.create') }}" class="hover:text-ink">Request Quotation</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
