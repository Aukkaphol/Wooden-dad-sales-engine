@extends('layouts.admin', ['title' => $po->po_number.' | '.company()->display_name])

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.purchase.index') }}" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
            <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $po->po_number }}</h1>
            <p class="mt-2 text-sm text-pine-700">{{ $po->supplier->supplier_name }} · {{ $po->status_label }}</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20">{{ $errors->first() }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ข้อมูลใบสั่งซื้อ</h2>
                    <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2">
                        <div><dt class="font-medium text-pine-500">วันที่สั่งซื้อ</dt><dd class="mt-1 font-semibold text-ink">{{ $po->order_date->format('d/m/Y') }}</dd></div>
                        <div><dt class="font-medium text-pine-500">กำหนดส่ง</dt><dd class="mt-1 font-semibold text-ink">{{ $po->expected_delivery_date?->format('d/m/Y') ?? '-' }}</dd></div>
                        <div><dt class="font-medium text-pine-500">อ้างอิง PR</dt><dd class="mt-1 font-semibold text-ink">{{ $po->purchaseRequisition?->pr_number ?? '-' }}</dd></div>
                        <div><dt class="font-medium text-pine-500">ยอดรวม</dt><dd class="mt-1 font-semibold text-ink">฿{{ number_format((float) $po->total_cost, 2) }}</dd></div>
                        <div class="md:col-span-2"><dt class="font-medium text-pine-500">หมายเหตุ</dt><dd class="mt-1 whitespace-pre-line font-semibold text-ink">{{ $po->notes ?: '-' }}</dd></div>
                    </dl>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">รายการวัตถุดิบ</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($po->items as $item)
                            @php
                                $remaining = max(0, (float) $item->quantity - (float) $item->received_quantity);
                            @endphp
                            <div class="rounded-md bg-pine-50 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-ink">{{ $item->material->name }}</p>
                                        <p class="mt-1 text-sm text-pine-700">สั่งซื้อ {{ number_format((float) $item->quantity, 3) }} {{ $item->unit }} · รับแล้ว {{ number_format((float) $item->received_quantity, 3) }} · ค้างรับ {{ number_format($remaining, 3) }}</p>
                                        <p class="mt-1 text-sm font-semibold text-pine-700">฿{{ number_format((float) $item->unit_cost, 2) }} / {{ $item->unit }}</p>
                                    </div>
                                    @if ($remaining > 0)
                                        <form method="post" action="{{ route('admin.purchase.receive', $item) }}" class="grid gap-2 sm:w-52">
                                            @csrf
                                            <input type="date" name="receive_date" value="{{ now()->format('Y-m-d') }}" class="rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                            <input type="number" step="0.001" min="0.001" max="{{ $remaining }}" name="received_quantity" value="{{ $remaining }}" class="rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                            <button class="rounded-md bg-pine-700 px-3 py-2 text-sm font-semibold text-white">รับเข้าคลัง</button>
                                        </form>
                                    @else
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">รับครบแล้ว</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ประวัติรับสินค้า</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($po->receipts as $receipt)
                        <div class="rounded-md bg-pine-50 p-4">
                            <p class="font-semibold text-ink">{{ $receipt->material->name }}</p>
                            <p class="mt-1 text-sm text-pine-700">{{ $receipt->receive_date->format('d/m/Y') }} · รับ {{ number_format((float) $receipt->received_quantity, 3) }}</p>
                        </div>
                    @empty
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีการรับสินค้า</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
