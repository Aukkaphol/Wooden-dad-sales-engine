@extends('layouts.admin', ['title' => 'สร้างใบเสนอราคา | '.company()->display_name])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="{{ $lead ? route('admin.leads.show', $lead) : route('admin.quotations.index') }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับ</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">สร้างใบเสนอราคา</h1>
                    <p class="mt-2 text-sm text-pine-700">เลือก Lead แล้วระบบจะเติมข้อมูลลูกค้าให้อัตโนมัติ</p>
                </div>
            </div>

            @include('admin.quotations._form', [
                'action' => $lead ? route('admin.leads.quotations.store', $lead) : route('admin.quotations.store'),
                'method' => 'POST',
                'submitLabel' => 'สร้างใบเสนอราคา',
            ])
        </div>
    </section>
@endsection
