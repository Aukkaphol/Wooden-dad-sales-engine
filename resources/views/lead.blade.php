@extends('layouts.app', ['title' => 'ขอราคาและแบบฟรี | Wooden Dad Design'])

@section('content')
    <section class="bg-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 lg:grid-cols-[.8fr_1.2fr]">
            <div>
                <p class="text-sm font-semibold text-pine-500">ขอราคาและแบบฟรี</p>
                <h1 class="mt-3 text-4xl font-semibold">เล่าเรื่องห้องของคุณให้เราฟัง</h1>
                <p class="mt-4 leading-8 text-pine-700">กรอกข้อมูลเบื้องต้น ทีม Wooden Dad Design จะช่วยประเมินแพ็กเกจ Bedroom Set ที่เหมาะกับพื้นที่ งบประมาณ และสไตล์ที่คุณต้องการ</p>
            </div>

            <form action="{{ route('lead.store') }}" method="post" enctype="multipart/form-data" class="rounded-lg border border-pine-200 bg-pine-50 p-6 shadow-sm">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold">ชื่อ</span>
                        <input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        @error('name') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">เบอร์โทร</span>
                        <input name="phone" value="{{ old('phone') }}" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        @error('phone') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">จังหวัด</span>
                        <input name="province" value="{{ old('province') }}" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        @error('province') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">งบประมาณ</span>
                        <select name="budget" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                            <option value="">เลือกงบประมาณ</option>
                            @foreach (['ต่ำกว่า 30,000 บาท', '30,000 - 60,000 บาท', '60,000 - 100,000 บาท', 'มากกว่า 100,000 บาท'] as $budget)
                                <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
                            @endforeach
                        </select>
                        @error('budget') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">ความกว้างห้อง (เมตร)</span>
                        <input name="room_width" type="number" step="0.01" min="0.1" value="{{ old('room_width') }}" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        @error('room_width') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">ความยาวห้อง (เมตร)</span>
                        <input name="room_length" type="number" step="0.01" min="0.1" value="{{ old('room_length') }}" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        @error('room_length') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label class="mt-5 block">
                    <span class="text-sm font-semibold">ข้อความเพิ่มเติม</span>
                    <textarea name="message" rows="4" class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">{{ old('message') }}</textarea>
                    @error('message') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="mt-5 block">
                    <span class="text-sm font-semibold">อัปโหลดรูปห้อง</span>
                    <input name="room_image" type="file" accept="image/*" class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none file:mr-4 file:rounded-full file:border-0 file:bg-pine-100 file:px-4 file:py-2 file:text-pine-700">
                    @error('room_image') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <button type="submit" class="mt-6 w-full rounded-full bg-pine-500 px-6 py-3 font-semibold text-white hover:bg-pine-700">ส่งข้อมูลเพื่อขอราคาและแบบฟรี</button>
            </form>
        </div>
    </section>
@endsection
