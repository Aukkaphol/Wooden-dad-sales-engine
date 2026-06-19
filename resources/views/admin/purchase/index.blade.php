@extends('layouts.app', ['title' => 'จัดซื้อ | Wooden Dad Design'])

@php($chartMax = max(1, $purchaseByMonth->max('value') ?? 1))

@section('content')
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">Purchase & Procurement</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">ระบบจัดซื้อวัตถุดิบ</h1>
                <p class="mt-2 text-sm text-pine-700">เชื่อมคลังวัสดุ BOM งานผลิต ใบขอซื้อ ใบสั่งซื้อ และการรับสินค้าเข้าคลัง</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.purchase.pr.create') }}" class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้าง PR</a>
                <a href="{{ route('admin.purchase.po.create') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">สร้าง PO</a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
        @endif

        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">PR เปิดอยู่</dt><dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($metrics['open_pr']) }}</dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">PO เปิดอยู่</dt><dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($metrics['open_po']) }}</dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">รอรับสินค้า</dt><dd class="mt-2 text-3xl font-semibold text-amber-700">{{ number_format($metrics['waiting_receipts']) }}</dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">วัตถุดิบต่ำกว่าขั้นต่ำ</dt><dd class="mt-2 text-3xl font-semibold text-rose-700">{{ number_format($metrics['low_stock']) }}</dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">ยอดซื้อเดือนนี้</dt><dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($metrics['purchase_value_this_month'], 2) }}</dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">ผู้จำหน่าย</dt><dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($metrics['supplier_count']) }}</dd></div>
        </dl>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ยอดจัดซื้อรายเดือน</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($purchaseByMonth as $row)
                        <div>
                            <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700">{{ $row->month }}</span><span class="font-semibold text-ink">฿{{ number_format((float) $row->value, 2) }}</span></div>
                            <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-amber-600" style="width: {{ max(6, ((float) $row->value / $chartMax) * 100) }}%"></div></div>
                        </div>
                    @empty
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มียอดจัดซื้อ</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ข้อเสนอซื้อจาก Low Stock</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($lowStockSuggestions as $row)
                        <div class="rounded-md bg-rose-50 p-4 ring-1 ring-rose-100">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-ink">{{ $row['material']->name }}</p>
                                    <p class="text-sm text-rose-700">คงเหลือ {{ number_format($row['current'], 3) }} / ขั้นต่ำ {{ number_format($row['minimum'], 3) }} {{ $row['material']->unit }}</p>
                                    <p class="mt-1 text-sm font-semibold text-rose-800">แนะนำซื้อ {{ number_format($row['suggested_quantity'], 3) }} {{ $row['material']->unit }}</p>
                                </div>
                                <form method="post" action="{{ route('admin.purchase.auto-pr.material', $row['material']) }}">
                                    @csrf
                                    <button class="rounded-md bg-pine-700 px-3 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้าง PR อัตโนมัติ</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีวัตถุดิบต่ำกว่าขั้นต่ำ</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-2">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ใบขอซื้อล่าสุด</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($purchaseRequisitions as $pr)
                        <a href="{{ route('admin.purchase.pr.show', $pr) }}" class="block rounded-md bg-pine-50 p-4 hover:bg-pine-100">
                            <div class="flex justify-between gap-3"><p class="font-semibold text-ink">{{ $pr->pr_number }}</p><p class="text-sm font-semibold text-pine-700">{{ $pr->status_label }}</p></div>
                            <p class="mt-1 text-sm text-pine-700">{{ $pr->requested_by }} · {{ $pr->request_date->format('d/m/Y') }}</p>
                        </a>
                    @empty
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มี PR</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ใบสั่งซื้อล่าสุด</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($purchaseOrders as $po)
                        <a href="{{ route('admin.purchase.po.show', $po) }}" class="block rounded-md bg-pine-50 p-4 hover:bg-pine-100">
                            <div class="flex justify-between gap-3"><p class="font-semibold text-ink">{{ $po->po_number }}</p><p class="text-sm font-semibold text-pine-700">฿{{ number_format((float) $po->total_cost, 2) }}</p></div>
                            <p class="mt-1 text-sm text-pine-700">{{ $po->supplier->supplier_name }} · {{ $po->status_label }}</p>
                        </a>
                    @empty
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มี PO</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <h2 class="text-lg font-semibold text-ink">รายงานจัดซื้อ</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach (['purchase-summary' => 'สรุปการจัดซื้อ', 'supplier-summary' => 'สรุปตามผู้จำหน่าย', 'material-consumption' => 'การใช้วัสดุ', 'low-stock' => 'วัตถุดิบใกล้หมด', 'outstanding-po' => 'PO ค้างรับ'] as $type => $label)
                    <div class="rounded-md bg-pine-50 p-4">
                        <p class="font-semibold text-ink">{{ $label }}</p>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.purchase.reports.export', [$type, 'excel']) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-pine-700 ring-1 ring-pine-200">Excel</a>
                            <a href="{{ route('admin.purchase.reports.export', [$type, 'pdf']) }}" class="rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-pine-700 ring-1 ring-pine-200">PDF</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</section>
@endsection
