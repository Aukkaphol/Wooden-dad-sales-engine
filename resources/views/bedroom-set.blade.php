@extends('layouts.public', ['title' => __('messages.nav.bedroom_sets').' | '.company()->display_name])

@php
    $en = app()->getLocale() === 'en';
    $packages = $en ? [
        [
            'name' => 'Bedroom Set S',
            'description' => 'A compact starter set for small rooms or new bedroom styling with essential pine wood functions.',
            'items' => ['Simple pine bed', '1 bedside table', 'Shelf or end-of-bed bench'],
        ],
        [
            'name' => 'Bedroom Set M',
            'recommended' => 'Recommended Package',
            'description' => 'A balanced main-bedroom package with storage, beauty, and budget in one warm pine wood style.',
            'items' => ['Pine bed with headboard', '2 bedside tables', 'Matching wardrobe or storage shelf', 'Adjustable tone and size'],
        ],
        [
            'name' => 'Bedroom Set Premium',
            'description' => 'A detailed custom set for rooms that need complete function and a built-in style atmosphere.',
            'items' => ['Special-design pine bed', 'Full-room storage system', 'Matching dressing table or work desk', 'Detailed custom design from real room measurements'],
        ],
    ] : [
        [
            'name' => 'Bedroom Set S',
            'description' => 'เหมาะกับห้องเล็กหรือเริ่มต้นจัดห้องใหม่ ได้ฟังก์ชันจำเป็นในโทนไม้สนอบอุ่น',
            'items' => ['เตียงไม้สนเรียบง่าย', 'โต๊ะหัวเตียง 1 ชิ้น', 'ชั้นวางของหรือม้านั่งปลายเตียง'],
        ],
        [
            'name' => 'Bedroom Set M',
            'recommended' => 'แพ็กเกจแนะนำ',
            'description' => 'เหมาะกับห้องนอนหลัก ให้ความลงตัวระหว่างพื้นที่เก็บของ ความสวยงาม และงบประมาณ',
            'items' => ['เตียงไม้สนพร้อมหัวเตียง', 'โต๊ะหัวเตียง 2 ชิ้น', 'ตู้เสื้อผ้าหรือชั้นเก็บของเข้าชุด', 'ปรับโทนสีและขนาดได้'],
        ],
        [
            'name' => 'Bedroom Set Premium',
            'description' => 'สำหรับห้องที่ต้องการงานละเอียด ฟังก์ชันครบ และภาพรวมที่ดูอบอุ่นเหมือนบิลต์อิน',
            'items' => ['เตียงไม้สนดีไซน์พิเศษ', 'ชุดตู้และชั้นเก็บของรอบห้อง', 'โต๊ะเครื่องแป้งหรือโต๊ะทำงานเข้าชุด', 'ออกแบบตามพื้นที่จริงแบบละเอียด'],
        ],
    ];
@endphp

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-5 py-14">
            <p class="text-sm font-semibold text-pine-500">Bedroom Set Collection</p>
            <h1 class="mt-3 text-4xl font-semibold">{{ $en ? 'Choose a bedroom set designed for your real space' : 'เลือกชุดห้องนอนที่เหมาะกับพื้นที่ของคุณ' }}</h1>
            <p class="mt-4 max-w-2xl leading-8 text-pine-700">{{ $en ? 'Every package can be adjusted by size, details, room dimensions, and budget. Send your room information and our team will prepare an initial design estimate for free.' : 'ทุกแพ็กเกจปรับขนาดและรายละเอียดได้ตามพื้นที่จริง ส่งขนาดห้อง รูปห้อง และงบประมาณมาให้เราออกแบบเบื้องต้นฟรี' }}</p>
        </div>
    </section>

    <section class="bg-pine-50">
        <div class="mx-auto grid max-w-6xl gap-5 px-5 py-12 lg:grid-cols-3">
            @foreach ($packages as $package)
                <article class="{{ isset($package['recommended']) ? 'border-2 border-pine-500 shadow-md' : 'border border-pine-200 shadow-sm' }} rounded-lg bg-white p-6">
                    @isset($package['recommended'])
                        <div class="mb-4 inline-flex rounded-full bg-pine-100 px-3 py-1 text-sm font-semibold text-pine-700">{{ $package['recommended'] }}</div>
                    @endisset
                    <h2 class="text-2xl font-semibold">{{ $package['name'] }}</h2>
                    <p class="mt-3 leading-7 text-pine-700">{{ $package['description'] }}</p>
                    <ul class="mt-5 space-y-3 text-pine-700">
                        @foreach ($package['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-5 px-5 py-12 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-semibold">{{ $en ? 'Want to know which package fits your room?' : 'อยากรู้ว่าแพ็กเกจไหนเหมาะกับห้องคุณ?' }}</h2>
                <p class="mt-3 text-pine-700">{{ $en ? 'Send room size, room photo, and budget. Our team will help estimate for free.' : 'ส่งขนาดห้อง รูปห้อง และงบประมาณ ทีมงานช่วยประเมินให้ฟรี' }}</p>
            </div>
            <a href="{{ route('lead.create') }}" class="inline-flex items-center justify-center rounded-full bg-pine-500 px-6 py-3 font-semibold text-white hover:bg-pine-700">{{ __('messages.nav.request_quotation') }}</a>
        </div>
    </section>
@endsection
