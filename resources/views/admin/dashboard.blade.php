@extends('layouts.app', ['title' => 'แดชบอร์ดผู้บริหาร | Wooden Dad Design'])

@php
    $leadMax = max(1, $leadsByMonth->max('value') ?? 1);
    $revenueMax = max(1, $revenueByMonth->max('value') ?? 1);
    $profitMax = max(1, $profitByMonth->max('value') ?? 1);
    $productionLabels = \App\Models\ProductionOrder::STATUSES;
@endphp

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">แดชบอร์ดผู้บริหาร</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ภาพรวมยอดขาย กำไร และงานผลิต</h1>
                    <p class="mt-2 max-w-3xl text-sm text-pine-700">ติดตามยอดขายรายเดือน กำไร ใบเสนอราคาที่รอดำเนินการ คิวผลิต วัสดุใกล้หมด และสินค้าขายดีของ Wooden Dad Design</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดูแคตตาล็อกสินค้า</a>
            </div>

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดทั้งหมด</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($metrics['total_leads']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ใบเสนอราคาทั้งหมด</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($metrics['total_quotations']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">รอติดตามใบเสนอราคา</dt>
                    <dd class="mt-2 text-3xl font-semibold text-amber-700">{{ number_format($metrics['pending_quotations']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">อนุมัติแล้ว</dt>
                    <dd class="mt-2 text-3xl font-semibold text-emerald-700">{{ number_format($metrics['approved_quotations']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">คิวงานผลิต</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink">{{ number_format($metrics['production_orders']) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ยอดขายรวม</dt>
                    <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($metrics['total_revenue'], 2) }}</dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">กำไรคาดการณ์</dt>
                    <dd class="mt-2 text-2xl font-semibold text-emerald-700">฿{{ number_format($metrics['estimated_profit'], 2) }}</dd>
                </div>
            </dl>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ลีดรายเดือน</h2>
                    <div class="mt-5 space-y-4">
                        @forelse ($leadsByMonth as $row)
                            <div>
                                <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700">{{ $row['label'] }}</span><span class="font-semibold text-ink">{{ number_format($row['value']) }}</span></div>
                                <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-pine-500" style="width: {{ max(6, ($row['value'] / $leadMax) * 100) }}%"></div></div>
                            </div>
                        @empty
                            <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีข้อมูลลีด</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ยอดขายรายเดือน</h2>
                    <div class="mt-5 space-y-4">
                        @forelse ($revenueByMonth as $row)
                            <div>
                                <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700">{{ $row['label'] }}</span><span class="font-semibold text-ink">฿{{ number_format($row['value'], 2) }}</span></div>
                                <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-emerald-500" style="width: {{ max(6, ($row['value'] / $revenueMax) * 100) }}%"></div></div>
                            </div>
                        @empty
                            <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มียอดขายที่อนุมัติแล้ว</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">กำไรรายเดือน</h2>
                    <div class="mt-5 space-y-4">
                        @forelse ($profitByMonth as $row)
                            <div>
                                <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700">{{ $row['label'] }}</span><span class="font-semibold text-ink">฿{{ number_format($row['value'], 2) }}</span></div>
                                <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-amber-500" style="width: {{ max(6, ($row['value'] / $profitMax) * 100) }}%"></div></div>
                            </div>
                        @empty
                            <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีข้อมูลกำไร</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">สรุปคิวงานผลิต</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach ($productionLabels as $status => $label)
                            <div class="rounded-md bg-pine-50 p-4">
                                <p class="text-sm font-medium text-pine-700">{{ $label }}</p>
                                <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($productionSummary[$status] ?? 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">วัสดุใกล้หมด</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($lowStockSuggestions as $row)
                            @php($material = $row['material'])
                            <div class="flex flex-col gap-2 rounded-md bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-ink">{{ $material->name }}</p>
                                    <p class="text-sm text-pine-700">ขั้นต่ำ {{ number_format((float) $material->low_stock_level, 3) }} {{ $material->unit }}</p>
                                    <p class="mt-1 text-sm font-semibold text-amber-700">แนะนำซื้อ {{ number_format($row['suggested_quantity'], 3) }} {{ $material->unit }}</p>
                                </div>
                                <form method="post" action="{{ route('admin.purchase.auto-pr.material', $material) }}" class="shrink-0">
                                    @csrf
                                    <button class="rounded-md bg-pine-700 px-3 py-2 text-sm font-semibold text-white">สร้าง PR</button>
                                </form>
                            </div>
                        @empty
                            <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ไม่มีวัสดุใกล้หมด</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-2">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">สินค้าขายดี</h2>
                    <div class="mt-5 grid gap-3 md:hidden">
                        @forelse ($topSellingProducts as $product)
                            <article class="rounded-md bg-pine-50 p-4">
                                <div class="flex justify-between gap-3"><p class="font-semibold text-ink">{{ $product['product_name'] }}</p><p class="text-sm font-semibold text-pine-700">{{ $product['sku'] ?? '-' }}</p></div>
                                <p class="mt-2 text-sm text-pine-700">จำนวนขาย {{ number_format($product['quantity_sold'], 2) }} ชิ้น</p>
                                <p class="mt-1 text-sm font-semibold text-emerald-700">ยอดขาย ฿{{ number_format($product['revenue'], 2) }}</p>
                            </article>
                        @empty
                            <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีข้อมูลสินค้า</p>
                        @endforelse
                    </div>
                    <table class="mt-5 hidden min-w-full table-fixed divide-y divide-pine-200 text-sm md:table">
                        <thead class="bg-pine-100 text-pine-700">
                            <tr>
                                <th class="w-24 px-3 py-2 text-left font-semibold">SKU</th>
                                <th class="px-3 py-2 text-left font-semibold">สินค้า</th>
                                <th class="px-3 py-2 text-right font-semibold">จำนวนขาย</th>
                                <th class="px-3 py-2 text-right font-semibold">ยอดขาย</th>
                                <th class="px-3 py-2 text-right font-semibold">กำไร</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pine-100">
                            @forelse ($topSellingProducts as $product)
                                <tr>
                                    <td class="px-3 py-3 font-semibold text-pine-700">{{ $product['sku'] ?? '-' }}</td>
                                    <td class="px-3 py-3 font-medium text-ink">{{ $product['product_name'] }}</td>
                                    <td class="px-3 py-3 text-right text-pine-700">{{ number_format($product['quantity_sold'], 2) }}</td>
                                    <td class="px-3 py-3 text-right text-pine-700">฿{{ number_format($product['revenue'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-emerald-700">฿{{ number_format($product['profit'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-8 text-center text-pine-700">ยังไม่มีข้อมูลสินค้า</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ลูกค้ารายได้สูงสุด</h2>
                    <div class="mt-5 grid gap-3 md:hidden">
                        @forelse ($topCustomers as $customer)
                            <article class="rounded-md bg-pine-50 p-4">
                                <p class="font-semibold text-ink">{{ $customer['customer'] }}</p>
                                <p class="mt-2 text-sm text-pine-700">ใบเสนอราคา {{ number_format($customer['quotation_count']) }} ใบ</p>
                                <p class="mt-1 text-sm font-semibold text-emerald-700">ยอดขาย ฿{{ number_format($customer['revenue'], 2) }}</p>
                            </article>
                        @empty
                            <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีข้อมูลลูกค้า</p>
                        @endforelse
                    </div>
                    <table class="mt-5 hidden min-w-full table-fixed divide-y divide-pine-200 text-sm md:table">
                        <thead class="bg-pine-100 text-pine-700">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">ลูกค้า</th>
                                <th class="px-3 py-2 text-right font-semibold">จำนวนใบเสนอราคา</th>
                                <th class="px-3 py-2 text-right font-semibold">ยอดขาย</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pine-100">
                            @forelse ($topCustomers as $customer)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-ink">{{ $customer['customer'] }}</td>
                                    <td class="px-3 py-3 text-right text-pine-700">{{ number_format($customer['quotation_count']) }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-emerald-700">฿{{ number_format($customer['revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-8 text-center text-pine-700">ยังไม่มีข้อมูลลูกค้า</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </section>
@endsection
