@extends('layouts.app', ['title' => 'เข้าสู่ระบบแอดมิน | '.company()->display_name])

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-md px-5 py-16">
            <h1 class="text-3xl font-semibold">เข้าสู่ระบบแอดมิน</h1>
            <p class="mt-3 text-pine-700">สำหรับดูรายชื่อลูกค้าที่กรอกฟอร์ม</p>

            <form action="{{ route('login.store') }}" method="post" class="mt-8 rounded-lg border border-pine-200 bg-pine-50 p-6">
                @csrf

                <label class="block">
                    <span class="text-sm font-semibold">อีเมล</span>
                    <input name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                    @error('email') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="mt-5 block">
                    <span class="text-sm font-semibold">รหัสผ่าน</span>
                    <input name="password" type="password" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                    @error('password') <span class="mt-1 block text-sm text-red-700">{{ $message }}</span> @enderror
                </label>

                <label class="mt-5 flex items-center gap-2 text-sm text-pine-700">
                    <input name="remember" type="checkbox" value="1" class="rounded border-pine-300">
                    จดจำการเข้าสู่ระบบ
                </label>

                <button type="submit" class="mt-6 w-full rounded-full bg-pine-500 px-6 py-3 font-semibold text-white hover:bg-pine-700">เข้าสู่ระบบ</button>
            </form>
        </div>
    </section>
@endsection
