@extends('layouts.admin', ['title' => 'เพิ่มสินค้า | '.company()->display_name])
@section('content')
<section class="bg-pine-50"><div class="mx-auto max-w-4xl px-4 py-8"><a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-pine-700">กลับ</a><h1 class="mt-2 mb-6 text-3xl font-semibold text-ink">เพิ่มสินค้า</h1>@include('admin.products._form', ['action' => route('admin.products.store'), 'method' => 'POST', 'submitLabel' => 'บันทึกสินค้า'])</div></section>
@endsection
