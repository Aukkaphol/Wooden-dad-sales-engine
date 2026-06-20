@extends('layouts.admin', ['title' => ($supplier->exists ? 'แก้ไขผู้จำหน่าย' : 'เพิ่มผู้จำหน่าย').' | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับทะเบียนผู้จำหน่าย</a>
            <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $supplier->exists ? 'แก้ไขผู้จำหน่าย' : 'เพิ่มผู้จำหน่าย' }}</h1>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            @csrf
            @if ($supplier->exists)
                @method('PUT')
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <label><span class="text-sm font-semibold text-ink">รหัสผู้จำหน่าย</span><input name="supplier_code" value="{{ old('supplier_code', $supplier->supplier_code) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">ชื่อผู้จำหน่าย</span><input name="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">ผู้ติดต่อ</span><input name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">เบอร์โทร</span><input name="phone" value="{{ old('phone', $supplier->phone) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">LINE ID</span><input name="line_id" value="{{ old('line_id', $supplier->line_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">อีเมล</span><input name="email" value="{{ old('email', $supplier->email) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">เลขประจำตัวผู้เสียภาษี</span><input name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label class="flex items-center gap-3 pt-7"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $supplier->is_active)) class="rounded border-pine-300"><span class="text-sm font-semibold text-ink">เปิดใช้งานผู้จำหน่าย</span></label>
                <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">ที่อยู่</span><textarea name="address" rows="3" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">{{ old('address', $supplier->address) }}</textarea></label>
                <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">หมายเหตุ</span><textarea name="notes" rows="3" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">{{ old('notes', $supplier->notes) }}</textarea></label>
            </div>

            <button class="mt-6 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกผู้จำหน่าย</button>
        </form>
    </div>
</section>
@endsection
