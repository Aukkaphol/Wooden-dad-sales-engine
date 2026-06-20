@extends('layouts.public', ['title' => (app()->getLocale() === 'en' ? 'Thank You' : 'ขอบคุณ').' | '.company()->display_name])

@php($en = app()->getLocale() === 'en')

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-3xl px-5 py-20 text-center">
            <p class="text-sm font-semibold text-pine-500">{{ $en ? 'Request submitted' : 'ส่งข้อมูลเรียบร้อย' }}</p>
            <h1 class="mt-3 text-4xl font-semibold">{{ $en ? 'Thank you for your interest in '.company()->display_name : 'ขอบคุณที่สนใจ '.company()->display_name }}</h1>
            <p class="mt-5 leading-8 text-pine-700">{{ $en ? 'Our team will review your room information and contact you soon with a suitable initial estimate.' : 'ทีมงานจะตรวจข้อมูลห้องของคุณและติดต่อกลับเพื่อเสนอแพ็กเกจที่เหมาะสมโดยเร็วที่สุด' }}</p>
            <a href="{{ route('home') }}" class="mt-8 inline-flex rounded-full border border-pine-300 bg-pine-50 px-6 py-3 font-semibold text-pine-700 hover:border-pine-500">{{ __('messages.nav.home') }}</a>
        </div>
    </section>
@endsection
