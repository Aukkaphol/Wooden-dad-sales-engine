@extends('layouts.admin', ['title' => 'แก้ไขใบเสนอราคา | '.company()->display_name])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="{{ route('admin.quotations.show', $quotation) }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปหน้ารายละเอียด</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">แก้ไขใบเสนอราคา {{ $quotation->display_number }}</h1>
                    <p class="mt-2 text-sm text-pine-700">ปรับรายการสินค้า ส่วนลด ค่าขนส่ง เงินมัดจำ และหมายเหตุ</p>
                </div>
            </div>

            @include('admin.quotations._form', [
                'action' => route('admin.quotations.update', $quotation),
                'method' => 'PUT',
                'submitLabel' => 'บันทึกการแก้ไข',
            ])
        </div>
    </section>
@endsection
