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
<body class="min-h-screen bg-pine-50 text-ink antialiased">
    <header class="border-b border-pine-200/70 bg-white/85 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-normal text-pine-700">{{ $company->display_name }}</a>
            <nav class="flex flex-wrap items-center justify-center gap-3 text-sm font-medium text-pine-700 sm:justify-end sm:gap-4">
                <a href="{{ route('home') }}" class="hover:text-ink">หน้าแรก</a>
                <a href="{{ route('bedroom-set') }}" class="hover:text-ink">ชุดห้องนอน</a>
                <a href="{{ route('portfolio.index') }}" class="hover:text-ink">ผลงาน</a>
                <a href="{{ route('reviews.index') }}" class="hover:text-ink">รีวิวลูกค้า</a>
                <a href="{{ route('lead.create') }}" class="rounded-full bg-pine-500 px-4 py-2 text-white hover:bg-pine-700">ขอราคาและแบบฟรี</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-ink">แดชบอร์ด</a>
                    <a href="{{ route('admin.leads.index') }}" class="hover:text-ink">CRM ลูกค้า</a>
                    <a href="{{ route('admin.production.index') }}" class="hover:text-ink">งานผลิต</a>
                    <a href="{{ route('admin.installation.index') }}" class="hover:text-ink">ตารางติดตั้ง</a>
                    <a href="{{ route('admin.products.index') }}" class="hover:text-ink">สินค้า</a>
                    <a href="{{ route('admin.inventory.index') }}" class="hover:text-ink">คลังวัสดุ</a>
                    <a href="{{ route('admin.purchase.index') }}" class="hover:text-ink">จัดซื้อ</a>
                    <a href="{{ route('admin.suppliers.index') }}" class="hover:text-ink">ผู้จำหน่าย</a>
                    <a href="{{ route('admin.marketing.homepage') }}" class="hover:text-ink">การตลาด</a>
                    <a href="{{ route('admin.marketing.reviews.index') }}" class="hover:text-ink">รีวิวลูกค้า</a>
                    <a href="{{ route('admin.portfolio.index') }}" class="hover:text-ink">ผลงาน</a>
                    <a href="{{ route('admin.settings.facebook.edit') }}" class="hover:text-ink">เชื่อมต่อ Facebook</a>
                    <a href="{{ route('admin.settings.line.edit') }}" class="hover:text-ink">ตั้งค่า LINE OA</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-pine-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-5 py-8 text-sm text-pine-700 md:flex-row md:items-center md:justify-between">
            <p>{{ $company->display_name }} เฟอร์นิเจอร์ไม้สนสไตล์เรียบ อบอุ่น ใช้งานจริง</p>
            <p>ผลิตตามพื้นที่จริง ส่งงานด้วยรายละเอียดที่คุยกันชัดเจน</p>
        </div>
    </footer>
</body>
</html>
