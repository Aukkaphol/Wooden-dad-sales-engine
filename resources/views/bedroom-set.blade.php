@extends('layouts.app', ['title' => 'ชุดห้องนอน | Wooden Dad Design'])

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-6xl px-5 py-14">
            <p class="text-sm font-semibold text-pine-500">Bedroom Set Collection</p>
            <h1 class="mt-3 text-4xl font-semibold">เลือกชุดห้องนอนที่เหมาะกับพื้นที่ของคุณ</h1>
            <p class="mt-4 max-w-2xl leading-8 text-pine-700">ทุกแพ็กเกจปรับขนาดและรายละเอียดได้ตามพื้นที่จริง ส่งขนาดห้องและงบประมาณมาให้เราออกแบบเบื้องต้นฟรี</p>
        </div>
    </section>

    <section class="bg-pine-50">
        <div class="mx-auto grid max-w-6xl gap-5 px-5 py-12 lg:grid-cols-3">
            <article class="rounded-lg border border-pine-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-semibold">Bedroom Set S</h2>
                <p class="mt-3 leading-7 text-pine-700">เหมาะกับห้องเล็กหรือเริ่มต้นจัดห้องใหม่ ได้ฟังก์ชันจำเป็นในโทนไม้สนอบอุ่น</p>
                <ul class="mt-5 space-y-3 text-pine-700">
                    <li>เตียงไม้สนเรียบง่าย</li>
                    <li>โต๊ะหัวเตียง 1 ชิ้น</li>
                    <li>ชั้นวางของหรือม้านั่งปลายเตียง</li>
                </ul>
            </article>

            <article class="rounded-lg border-2 border-pine-500 bg-white p-6 shadow-md">
                <div class="mb-4 inline-flex rounded-full bg-pine-100 px-3 py-1 text-sm font-semibold text-pine-700">แพ็กเกจแนะนำ</div>
                <h2 class="text-2xl font-semibold">Bedroom Set M</h2>
                <p class="mt-3 leading-7 text-pine-700">เหมาะกับห้องนอนหลัก ให้ความลงตัวระหว่างพื้นที่เก็บของ ความสวยงาม และงบประมาณ</p>
                <ul class="mt-5 space-y-3 text-pine-700">
                    <li>เตียงไม้สนพร้อมหัวเตียง</li>
                    <li>โต๊ะหัวเตียง 2 ชิ้น</li>
                    <li>ตู้เสื้อผ้าหรือชั้นเก็บของเข้าชุด</li>
                    <li>ปรับโทนสีและขนาดได้</li>
                </ul>
            </article>

            <article class="rounded-lg border border-pine-200 bg-white p-6 shadow-sm">
                <h2 class="text-2xl font-semibold">Bedroom Set Premium</h2>
                <p class="mt-3 leading-7 text-pine-700">สำหรับห้องที่ต้องการงานละเอียด ฟังก์ชันครบ และภาพรวมที่ดูอบอุ่นเหมือนบิลต์อิน</p>
                <ul class="mt-5 space-y-3 text-pine-700">
                    <li>เตียงไม้สนดีไซน์พิเศษ</li>
                    <li>ชุดตู้และชั้นเก็บของรอบห้อง</li>
                    <li>โต๊ะเครื่องแป้งหรือโต๊ะทำงานเข้าชุด</li>
                    <li>ออกแบบตามพื้นที่จริงแบบละเอียด</li>
                </ul>
            </article>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-5 px-5 py-12 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-semibold">อยากรู้ว่าแพ็กเกจไหนเหมาะกับห้องคุณ?</h2>
                <p class="mt-3 text-pine-700">ส่งขนาดห้อง รูปห้อง และงบประมาณ ทีมงานช่วยประเมินให้ฟรี</p>
            </div>
            <a href="{{ route('lead.create') }}" class="inline-flex items-center justify-center rounded-full bg-pine-500 px-6 py-3 font-semibold text-white hover:bg-pine-700">ขอราคาและแบบฟรี</a>
        </div>
    </section>
@endsection
