@extends('layouts.admin', ['title' => $pr->pr_number.' | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.purchase.index') }}" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $pr->pr_number }}</h1>
                <p class="mt-2 text-sm text-pine-700">{{ $pr->request_date->format('d/m/Y') }} · {{ $pr->status_label }}</p>
            </div>
            <a href="{{ route('admin.purchase.po.create', ['pr' => $pr->id]) }}" class="w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white">สร้าง PO</a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_300px]">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">รายการวัตถุดิบที่ขอซื้อ</h2>
                <div class="mt-5 space-y-3">
                    @foreach ($pr->items as $item)
                        <div class="rounded-md bg-pine-50 p-4">
                            <div class="flex justify-between gap-3">
                                <p class="font-semibold text-ink">{{ $item->material->name }}</p>
                                <p class="font-semibold text-pine-700">{{ number_format((float) $item->quantity, 3) }} {{ $item->unit }}</p>
                            </div>
                            <p class="mt-1 text-sm text-pine-700">{{ $item->reason ?: $pr->reason }}</p>
                        </div>
                    @endforeach
                </div>
                <dl class="mt-6 grid gap-4 text-sm md:grid-cols-2">
                    <div><dt class="font-medium text-pine-500">ผู้ขอซื้อ</dt><dd class="mt-1 font-semibold text-ink">{{ $pr->requested_by }}</dd></div>
                    <div><dt class="font-medium text-pine-500">งานผลิตอ้างอิง</dt><dd class="mt-1 font-semibold text-ink">{{ $pr->productionOrder?->production_order_number ?? '-' }}</dd></div>
                    <div class="md:col-span-2"><dt class="font-medium text-pine-500">เหตุผล</dt><dd class="mt-1 whitespace-pre-line font-semibold text-ink">{{ $pr->reason ?: '-' }}</dd></div>
                </dl>
            </section>

            <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">เปลี่ยนสถานะ PR</h2>
                <div class="mt-5 space-y-2">
                    @foreach (\App\Models\PurchaseRequisition::STATUSES as $value => $label)
                        <form method="post" action="{{ route('admin.purchase.pr.status', $pr) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $value }}">
                            <button class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold ring-1 {{ $pr->status === $value ? 'bg-pine-100 text-pine-700 ring-pine-200' : 'bg-white text-pine-700 ring-pine-200 hover:bg-pine-50' }}">{{ $label }}</button>
                        </form>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
