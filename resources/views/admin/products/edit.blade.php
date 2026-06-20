@extends('layouts.admin', ['title' => 'แก้ไขสินค้า | '.company()->display_name])
@section('content')
<section class="bg-pine-50"><div class="mx-auto max-w-4xl px-4 py-8"><a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-pine-700">กลับ</a><h1 class="mt-2 mb-6 text-3xl font-semibold text-ink">แก้ไขสินค้า</h1>@include('admin.products._form', ['action' => route('admin.products.update', $product), 'method' => 'PUT', 'submitLabel' => 'บันทึกการแก้ไข'])</div></section>
@endsection
