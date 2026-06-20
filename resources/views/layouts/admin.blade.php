<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $company = company();
    @endphp
    <title>{{ $title ?? $company->display_name.' Sales CRM' }}</title>
    @if ($company->favicon_url)
        <link rel="icon" href="{{ $company->favicon_url }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-thai:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-pine-50 text-ink antialiased">
    @php
        $currentLocale = app()->getLocale();
        $localizedPath = function (string $locale): string {
            $segments = request()->segments();

            if (isset($segments[0]) && in_array($segments[0], ['th', 'en'], true)) {
                $segments[0] = $locale;
            } else {
                array_unshift($segments, $locale);
            }

            $path = implode('/', $segments);
            $query = request()->getQueryString();

            return url($path).($query ? '?'.$query : '');
        };
        $menuGroups = [
            __('admin.groups.dashboard') => [
                ['label' => __('admin.nav.dashboard'), 'route' => 'admin.dashboard'],
            ],
            __('admin.groups.marketing') => [
                ['label' => __('admin.nav.overview'), 'route' => 'admin.dashboard'],
                ['label' => __('admin.nav.website_leads'), 'route' => 'admin.marketing.website-leads'],
                ['label' => __('admin.nav.facebook_leads'), 'route' => 'admin.marketing.facebook-leads'],
                ['label' => __('admin.nav.line_leads'), 'route' => 'admin.marketing.line-leads'],
                ['label' => __('admin.nav.reviews'), 'route' => 'admin.marketing.reviews.index'],
                ['label' => __('admin.nav.portfolio'), 'route' => 'admin.portfolio.index'],
                ['label' => __('admin.nav.campaigns'), 'route' => 'admin.marketing.campaigns'],
                ['label' => __('admin.nav.analytics'), 'route' => 'admin.marketing.analytics'],
            ],
            __('admin.groups.sales') => [
                ['label' => __('admin.nav.crm_customers'), 'route' => 'admin.leads.index'],
                ['label' => __('admin.nav.quotations'), 'route' => 'admin.quotations.index'],
                ['label' => __('admin.nav.customers'), 'route' => 'admin.leads.index'],
            ],
            __('admin.groups.production') => [
                ['label' => __('admin.nav.production'), 'route' => 'admin.production.index'],
                ['label' => __('admin.nav.work_schedule'), 'route' => 'admin.production.index'],
                ['label' => __('admin.nav.installation_schedule'), 'route' => 'admin.installation.index'],
            ],
            __('admin.groups.inventory') => [
                ['label' => __('admin.nav.products'), 'route' => 'admin.products.index'],
                ['label' => __('admin.nav.bom'), 'route' => 'admin.products.index'],
                ['label' => __('admin.nav.materials'), 'route' => 'admin.materials.index'],
                ['label' => __('admin.nav.purchase_requests'), 'route' => 'admin.purchase-requests.index'],
            ],
            __('admin.groups.settings') => [
                ['label' => __('admin.nav.company_settings'), 'route' => 'admin.settings.company.edit', 'anchor' => 'company'],
                ['label' => __('admin.nav.social_media'), 'route' => 'admin.settings.company.edit', 'anchor' => 'social'],
                ['label' => __('admin.nav.line_settings'), 'route' => 'admin.settings.line.edit'],
                ['label' => __('admin.nav.facebook_settings'), 'route' => 'admin.settings.facebook.edit'],
                ['label' => __('admin.nav.website'), 'route' => 'admin.marketing.homepage'],
                ['label' => __('admin.nav.user_roles'), 'route' => 'admin.settings.users-roles'],
            ],
        ];
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[292px_1fr]">
        <aside class="sticky top-0 hidden h-screen overflow-y-auto border-r border-pine-200 bg-white/95 px-4 py-5 shadow-[10px_0_40px_rgba(93,66,39,.06)] backdrop-blur lg:block">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-2xl bg-pine-50 p-3">
                @if ($company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="{{ $company->display_name }}" class="h-11 w-11 rounded-full bg-white object-contain ring-1 ring-pine-200">
                @else
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-pine-700 text-sm font-semibold text-white">WD</span>
                @endif
                <span class="min-w-0">
                    <span class="block truncate font-semibold text-ink">{{ $company->display_name }}</span>
                    <span class="block text-xs font-medium text-pine-700">{{ __('admin.topbar.workspace') }}</span>
                </span>
            </a>

            <nav class="mt-6 space-y-6">
                @foreach ($menuGroups as $group => $items)
                    <section>
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-pine-500">{{ $group }}</p>
                        <div class="mt-2 grid gap-1">
                            @foreach ($items as $item)
                                @php
                                    $href = route($item['route']).(isset($item['anchor']) ? '#'.$item['anchor'] : '');
                                    $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
                                @endphp
                                <a href="{{ $href }}" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50 hover:text-ink {{ $active ? 'bg-pine-100 text-ink' : '' }}">{{ $item['label'] }}</a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </nav>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-40 border-b border-pine-200 bg-white/86 backdrop-blur-xl">
                <div class="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <details class="group relative lg:hidden">
                        <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl bg-pine-50 text-pine-800 ring-1 ring-pine-200 marker:hidden" aria-label="เปิดเมนูแอดมิน">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-open:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5 group-open:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </summary>
                        <div class="absolute left-0 top-14 max-h-[78vh] w-[min(88vw,360px)] overflow-y-auto rounded-2xl border border-pine-100 bg-white p-4 shadow-[0_24px_70px_rgba(28,23,18,.20)]">
                            <div class="mb-4 grid grid-cols-2 gap-2 rounded-2xl bg-pine-50 p-1 text-center text-xs font-semibold text-pine-700 ring-1 ring-pine-100">
                                <a href="{{ $localizedPath('th') }}" class="rounded-xl px-3 py-2 {{ $currentLocale === 'th' ? 'bg-white text-ink shadow-sm' : '' }}">TH</a>
                                <a href="{{ $localizedPath('en') }}" class="rounded-xl px-3 py-2 {{ $currentLocale === 'en' ? 'bg-white text-ink shadow-sm' : '' }}">EN</a>
                            </div>
                            @foreach ($menuGroups as $group => $items)
                                <section class="mb-4">
                                    <p class="px-2 text-xs font-semibold uppercase tracking-wide text-pine-500">{{ $group }}</p>
                                    <div class="mt-2 grid gap-1">
                                        @foreach ($items as $item)
                                            <a href="{{ route($item['route']).(isset($item['anchor']) ? '#'.$item['anchor'] : '') }}" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50">{{ $item['label'] }}</a>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>
                    </details>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-pine-500">{{ $company->display_name }}</p>
                        <h1 class="truncate text-lg font-semibold text-ink">{{ $title ?? __('admin.nav.dashboard') }}</h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden items-center rounded-full bg-pine-50 p-1 text-xs font-semibold text-pine-700 ring-1 ring-pine-200 sm:flex">
                            <a href="{{ $localizedPath('th') }}" class="rounded-full px-2.5 py-1 {{ $currentLocale === 'th' ? 'bg-white text-ink shadow-sm' : '' }}">TH</a>
                            <a href="{{ $localizedPath('en') }}" class="rounded-full px-2.5 py-1 {{ $currentLocale === 'en' ? 'bg-white text-ink shadow-sm' : '' }}">EN</a>
                        </div>
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-ink">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-pine-600">{{ __('admin.topbar.admin') }}</p>
                        </div>
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-50">{{ __('admin.nav.logout') }}</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="min-w-0">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
