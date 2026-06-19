@extends('layouts.app', ['title' => $quotation->quotation_number.' | Wooden Dad Design'])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('admin.leads.show', $quotation->lead) }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปหน้าลูกค้า</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">{{ $quotation->quotation_number }}</h1>
                    <p class="mt-2 text-sm text-pine-700">ใบเสนอราคา Wooden Dad Design สำหรับ {{ $quotation->lead->name }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.quotations.pdf', $quotation) }}" target="_blank" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ไฟล์ PDF</a>
                    <form action="{{ route('admin.quotations.status', $quotation) }}" method="post" class="flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected($quotation->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">อัปเดต</button>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
            @endif

            <div class="grid min-w-0 gap-6 lg:grid-cols-[.8fr_1.2fr]">
                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">ข้อมูลลูกค้า</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">ชื่อ</dt><dd class="font-semibold text-ink">{{ $quotation->lead->name }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">เบอร์โทร</dt><dd class="font-semibold text-ink">{{ $quotation->lead->phone }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">จังหวัด</dt><dd class="font-semibold text-ink">{{ $quotation->lead->province }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-pine-700">สถานะ</dt><dd class="font-semibold text-ink">{{ $quotation->status_label }}</dd></div>
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
                </section>

                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">รายการสินค้าในใบเสนอราคา</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-pine-200 text-sm">
                            <thead class="bg-pine-100 text-pine-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">สินค้า</th>
                                    <th class="px-3 py-2 text-right font-semibold">จำนวน</th>
                                    <th class="px-3 py-2 text-right font-semibold">ราคาต่อหน่วย</th>
                                    <th class="px-3 py-2 text-right font-semibold">ยอดรวม</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pine-100">
                                @foreach ($quotation->items as $item)
                                    <tr>
                                        <td class="px-3 py-3 font-medium text-ink">{{ $item->product_name }}</td>
                                        <td class="px-3 py-3 text-right text-pine-700">{{ $item->quantity }}</td>
                                        <td class="px-3 py-3 text-right text-pine-700">{{ number_format((float) $item->unit_price, 2) }}</td>
                                        <td class="px-3 py-3 text-right font-semibold text-ink">{{ number_format((float) $item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-right font-semibold text-pine-700">ยอดรวมใบเสนอราคา</td>
                                    <td class="px-3 py-4 text-right text-xl font-semibold text-ink">฿{{ number_format((float) $quotation->subtotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @if ($quotation->notes)
                        <div class="mt-5 rounded-md bg-pine-50 p-4">
                            <p class="text-sm font-semibold text-pine-700">หมายเหตุ</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-ink">{{ $quotation->notes }}</p>
                        </div>
                    @endif
                </section>
            </div>

            <section class="mt-6 min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">คำนวณต้นทุนและกำไร</h2>
                        <p class="mt-1 text-sm text-pine-700">ราคาขาย - ต้นทุนผลิต = กำไรขั้นต้น</p>
                    </div>
                    <p class="text-sm font-semibold text-pine-700">กำไร {{ number_format($costSummary['profit_percent'], 2) }}%</p>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-md bg-pine-50 p-4">
                        <dt class="text-sm font-medium text-pine-700">ยอดรวมใบเสนอราคา</dt>
                        <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['selling_price'], 2) }}</dd>
                    </div>
                    <div class="rounded-md bg-pine-50 p-4">
                        <dt class="text-sm font-medium text-pine-700">ต้นทุนคาดการณ์</dt>
                        <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['production_cost'], 2) }}</dd>
                    </div>
                    <div class="rounded-md bg-emerald-50 p-4">
                        <dt class="text-sm font-medium text-emerald-700">กำไรคาดการณ์</dt>
                        <dd class="mt-2 text-2xl font-semibold text-emerald-700">฿{{ number_format($costSummary['gross_profit'], 2) }}</dd>
                    </div>
                    <div class="rounded-md bg-pine-50 p-4">
                        <dt class="text-sm font-medium text-pine-700">มาร์จิ้นคาดการณ์ %</dt>
                        <dd class="mt-2 text-2xl font-semibold text-ink">{{ number_format($costSummary['profit_percent'], 2) }}%</dd>
                    </div>
                </dl>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-pine-200 text-sm">
                        <thead class="bg-pine-100 text-pine-700">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">สินค้า</th>
                                <th class="px-3 py-2 text-right font-semibold">วัสดุ</th>
                                <th class="px-3 py-2 text-right font-semibold">ค่าแรง</th>
                                <th class="px-3 py-2 text-right font-semibold">งานสี</th>
                                <th class="px-3 py-2 text-right font-semibold">ฮาร์ดแวร์</th>
                                <th class="px-3 py-2 text-right font-semibold">ต้นทุนผลิต</th>
                                <th class="px-3 py-2 text-right font-semibold">กำไร</th>
                                <th class="px-3 py-2 text-right font-semibold">กำไร %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pine-100">
                            @foreach ($costSummary['lines'] as $line)
                                <tr>
                                    <td class="px-3 py-3 font-medium text-ink">
                                        {{ $line['item']->product_name }}
                                        @unless ($line['product'])
                                            <span class="mt-1 block text-xs font-normal text-amber-700">ยังไม่พบสินค้า/BOM ที่ตรงกัน</span>
                                        @endunless
                                    </td>
                                    <td class="px-3 py-3 text-right text-pine-700">{{ number_format($line['material_cost'], 2) }}</td>
                                    <td class="px-3 py-3 text-right text-pine-700">{{ number_format($line['labor_cost'], 2) }}</td>
                                    <td class="px-3 py-3 text-right text-pine-700">{{ number_format($line['finishing_cost'], 2) }}</td>
                                    <td class="px-3 py-3 text-right text-pine-700">{{ number_format($line['hardware_cost'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-ink">{{ number_format($line['production_cost'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-emerald-700">{{ number_format($line['gross_profit'], 2) }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-ink">{{ number_format($line['profit_percent'], 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="px-3 py-4 font-semibold text-pine-700">รวม</td>
                                <td class="px-3 py-4 text-right font-semibold text-pine-700">{{ number_format($costSummary['material_cost'], 2) }}</td>
                                <td class="px-3 py-4 text-right font-semibold text-pine-700">{{ number_format($costSummary['labor_cost'], 2) }}</td>
                                <td class="px-3 py-4 text-right font-semibold text-pine-700">{{ number_format($costSummary['finishing_cost'], 2) }}</td>
                                <td class="px-3 py-4 text-right font-semibold text-pine-700">{{ number_format($costSummary['hardware_cost'], 2) }}</td>
                                <td class="px-3 py-4 text-right font-semibold text-ink">{{ number_format($costSummary['production_cost'], 2) }}</td>
                                <td class="px-3 py-4 text-right font-semibold text-emerald-700">{{ number_format($costSummary['gross_profit'], 2) }}</td>
                                <td class="px-3 py-4 text-right font-semibold text-ink">{{ number_format($costSummary['profit_percent'], 2) }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        </div>
    </section>
@endsection
