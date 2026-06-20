@extends('layouts.admin', ['title' => $quotation->display_number.' | '.company()->display_name])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('admin.quotations.index') }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปหน้ารายการใบเสนอราคา</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $quotation->display_number }}</h1>
                    <p class="mt-2 text-sm text-pine-700">{{ $quotation->project_name ?: 'ใบเสนอราคาเฟอร์นิเจอร์ไม้สนสั่งทำ' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.quotations.edit', $quotation) }}" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">Edit</a>
                    <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">PDF</a>
                    @if ($quotation->status !== 'approved')
                        <form method="post" action="{{ route('admin.quotations.approve', $quotation) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Approve</button>
                        </form>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[.85fr_1.15fr]">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ข้อมูลลูกค้า</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">ลูกค้า</dt><dd class="font-semibold text-ink">{{ $quotation->customer_name ?: $quotation->lead->name }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">เบอร์โทร</dt><dd class="font-semibold text-ink">{{ $quotation->phone ?: $quotation->lead->phone }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">จังหวัด</dt><dd class="font-semibold text-ink">{{ $quotation->province ?: $quotation->lead->province }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">สถานะ</dt><dd class="font-semibold text-ink">{{ $quotation->status_label }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">ใช้ได้ถึง</dt><dd class="font-semibold text-ink">{{ $quotation->valid_until?->format('d/m/Y') ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-pine-700">ใบสั่งผลิต</dt>
                            <dd class="font-semibold text-ink">
                                @if ($quotation->productionOrder)
                                    <a href="{{ route('admin.production.show', $quotation->productionOrder) }}" class="text-pine-700 hover:text-ink">{{ $quotation->productionOrder->production_order_number }}</a>
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <form action="{{ route('admin.quotations.status', $quotation) }}" method="post" class="mt-6 flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="min-w-0 flex-1 rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($quotation->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">อัปเดต</button>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">รายการสินค้า</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-pine-200 text-sm">
                            <thead class="bg-pine-100 text-pine-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">Item</th>
                                    <th class="px-3 py-2 text-left font-semibold">Description</th>
                                    <th class="px-3 py-2 text-right font-semibold">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold">Unit</th>
                                    <th class="px-3 py-2 text-right font-semibold">Unit Price</th>
                                    <th class="px-3 py-2 text-right font-semibold">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pine-100">
                                @foreach ($quotation->items as $item)
                                    <tr>
                                        <td class="px-3 py-3 font-medium text-ink">{{ $item->display_name }}</td>
                                        <td class="px-3 py-3 text-pine-700">{{ $item->description ?: '-' }}</td>
                                        <td class="px-3 py-3 text-right text-pine-700">{{ number_format($item->display_quantity, 2) }}</td>
                                        <td class="px-3 py-3 text-pine-700">{{ $item->unit }}</td>
                                        <td class="px-3 py-3 text-right text-pine-700">{{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-3 py-3 text-right font-semibold text-ink">{{ number_format($item->display_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <dl class="mt-6 space-y-3 rounded-2xl bg-pine-50 p-5 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">Subtotal</dt><dd class="font-semibold text-ink">฿{{ number_format((float) $quotation->subtotal, 2) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">Discount</dt><dd class="font-semibold text-ink">฿{{ number_format((float) $quotation->discount, 2) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">Shipping</dt><dd class="font-semibold text-ink">฿{{ number_format((float) $quotation->shipping_cost, 2) }}</dd></div>
                        <div class="flex justify-between gap-4 text-base"><dt class="font-semibold text-ink">Grand Total</dt><dd class="text-xl font-semibold text-ink">฿{{ number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">Deposit</dt><dd class="font-semibold text-ink">฿{{ number_format((float) $quotation->deposit_amount, 2) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">Balance</dt><dd class="font-semibold text-pine-700">฿{{ number_format($quotation->balance, 2) }}</dd></div>
                    </dl>

                    @if ($quotation->remark || $quotation->notes)
                        <div class="mt-5 rounded-xl bg-white p-4 ring-1 ring-pine-200">
                            <p class="text-sm font-semibold text-pine-700">หมายเหตุ</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-ink">{{ $quotation->remark ?: $quotation->notes }}</p>
                        </div>
                    @endif
                </section>
            </div>

            <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">ประมาณการต้นทุนและกำไร</h2>
                        <p class="mt-1 text-sm text-pine-700">คำนวณจาก BOM/Product cost เท่าที่ระบบจับคู่สินค้าได้</p>
                    </div>
                    <p class="text-sm font-semibold text-pine-700">Margin {{ number_format($costSummary['profit_percent'], 2) }}%</p>
                </div>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl bg-pine-50 p-4"><dt class="text-sm text-pine-700">Quotation Total</dt><dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['selling_price'], 2) }}</dd></div>
                    <div class="rounded-xl bg-pine-50 p-4"><dt class="text-sm text-pine-700">Estimated Cost</dt><dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['production_cost'], 2) }}</dd></div>
                    <div class="rounded-xl bg-emerald-50 p-4"><dt class="text-sm text-emerald-700">Estimated Profit</dt><dd class="mt-2 text-2xl font-semibold text-emerald-700">฿{{ number_format($costSummary['gross_profit'], 2) }}</dd></div>
                    <div class="rounded-xl bg-pine-50 p-4"><dt class="text-sm text-pine-700">Estimated Margin</dt><dd class="mt-2 text-2xl font-semibold text-ink">{{ number_format($costSummary['profit_percent'], 2) }}%</dd></div>
                </dl>
            </section>
        </div>
    </section>
@endsection
