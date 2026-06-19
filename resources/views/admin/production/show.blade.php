@extends('layouts.app', ['title' => $productionOrder->production_order_number.' | Wooden Dad Design'])

@php
    $statusButtons = [
        'waiting' => ['next' => 'cutting', 'label' => 'รอผลิต -> ตัดไม้'],
        'cutting' => ['next' => 'assembling', 'label' => 'ตัดไม้ -> ประกอบ'],
        'assembling' => ['next' => 'sanding', 'label' => 'ประกอบ -> ขัด'],
        'sanding' => ['next' => 'painting', 'label' => 'ขัด -> พ่นสี'],
        'painting' => ['next' => 'ready_delivery', 'label' => 'พ่นสี -> พร้อมส่งมอบ'],
        'ready_delivery' => ['next' => 'delivered', 'label' => 'พร้อมส่งมอบ -> ส่งมอบแล้ว'],
    ];
    $nextMove = $statusButtons[$productionOrder->status] ?? null;
    $address = $productionOrder->delivery_address ?: $productionOrder->lead->province;
    $installationStatuses = [
        'pending' => 'รอกำหนดวันติดตั้ง',
        'scheduled' => 'นัดติดตั้งแล้ว',
        'installed' => 'ติดตั้งเสร็จแล้ว',
        'delayed' => 'เลื่อนนัด',
    ];
@endphp

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="{{ route('admin.production.index') }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปบอร์ดงานผลิต</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">รายละเอียดใบสั่งผลิต</h1>
                    <p class="mt-2 text-sm text-pine-700">{{ $productionOrder->production_order_number }} · {{ $productionOrder->status_label }}</p>
                </div>
                <span class="w-fit rounded-full bg-pine-100 px-3 py-1.5 text-sm font-semibold text-pine-700 ring-1 ring-pine-200">{{ $productionOrder->status_label }}</span>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20">{{ session('success') }}</div>
            @endif

            @if ($materialShortages->isNotEmpty())
                <section class="mb-6 rounded-lg bg-rose-50 p-5 ring-1 ring-rose-200">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-rose-900">วัตถุดิบไม่เพียงพอ</h2>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($materialShortages as $shortage)
                                    <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-rose-700 ring-1 ring-rose-200">
                                        {{ $shortage['material']->name }} ขาด {{ number_format($shortage['shortage'], 3) }} {{ $shortage['material']->unit }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <form method="post" action="{{ route('admin.purchase.auto-pr.production', $productionOrder) }}">
                            @csrf
                            <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้าง PR อัตโนมัติ</button>
                        </form>
                    </div>
                </section>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
                <div class="min-w-0 space-y-6" style="min-width: 0; max-width: 100%;">
                    <section class="box-border max-w-full rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="box-sizing: border-box; max-width: 100%;">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ข้อมูลใบสั่งผลิต</h2>
                                <p class="mt-1 text-sm text-pine-700">เชื่อมกับข้อมูลลูกค้าและใบเสนอราคาที่อนุมัติแล้ว</p>
                            </div>
                            @if ($nextMove)
                                <form action="{{ route('admin.production.status', $productionOrder) }}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $nextMove['next'] }}">
                                    <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">{{ $nextMove['label'] }}</button>
                                </form>
                            @endif
                        </div>

                        <dl class="mt-6 grid gap-5 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-pine-500">เลขที่ใบสั่งผลิต</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $productionOrder->production_order_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">เลขที่ใบเสนอราคา</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('admin.quotations.show', $productionOrder->quotation) }}" class="font-semibold text-pine-700 hover:text-ink">{{ $productionOrder->quotation->quotation_number }}</a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">ชื่อลูกค้า</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('admin.leads.show', $productionOrder->lead) }}" class="font-semibold text-pine-700 hover:text-ink">{{ $productionOrder->lead->name }}</a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">เบอร์โทร</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $productionOrder->lead->phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">จังหวัด</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $productionOrder->lead->province }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">วันนัดส่งมอบ</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $productionOrder->delivery_date?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">วันนัดติดตั้ง</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $productionOrder->installation_date?->format('d/m/Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-pine-500">สถานะติดตั้ง</dt>
                                <dd class="mt-1 font-semibold text-ink">{{ $installationStatuses[$productionOrder->installation_status] ?? '-' }}</dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-pine-500">ที่อยู่จัดส่ง/ติดตั้ง</dt>
                                <dd class="mt-1 whitespace-pre-line font-semibold text-ink">{{ $address ?: '-' }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="box-border max-w-full rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="box-sizing: border-box; max-width: 100%;">
                        <h2 class="text-lg font-semibold text-ink">ต้นทุนงานผลิต</h2>
                        <dl class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-md bg-pine-50 p-4">
                                <dt class="text-sm font-medium text-pine-700">ยอดใบเสนอราคา</dt>
                                <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['selling_price'], 2) }}</dd>
                            </div>
                            <div class="rounded-md bg-pine-50 p-4">
                                <dt class="text-sm font-medium text-pine-700">ต้นทุนวัสดุ</dt>
                                <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['material_cost'], 2) }}</dd>
                            </div>
                            <div class="rounded-md bg-pine-50 p-4">
                                <dt class="text-sm font-medium text-pine-700">ค่าแรงผลิต</dt>
                                <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['labor_cost'], 2) }}</dd>
                            </div>
                            <div class="rounded-md bg-pine-50 p-4">
                                <dt class="text-sm font-medium text-pine-700">ค่าจัดส่ง/ติดตั้ง</dt>
                                <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['delivery_cost'], 2) }}</dd>
                            </div>
                            <div class="rounded-md bg-pine-50 p-4">
                                <dt class="text-sm font-medium text-pine-700">ต้นทุนรวม</dt>
                                <dd class="mt-2 text-2xl font-semibold text-ink">฿{{ number_format($costSummary['total_cost'], 2) }}</dd>
                            </div>
                            <div class="rounded-md bg-emerald-50 p-4">
                                <dt class="text-sm font-medium text-emerald-700">กำไรคาดการณ์</dt>
                                <dd class="mt-2 text-2xl font-semibold text-emerald-700">฿{{ number_format($costSummary['expected_profit'], 2) }}</dd>
                            </div>
                            <div class="rounded-md bg-emerald-50 p-4">
                                <dt class="text-sm font-medium text-emerald-700">มาร์จิ้นรวม</dt>
                                <dd class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($costSummary['gross_margin'], 2) }}%</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="box-border max-w-full rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="box-sizing: border-box; max-width: 100%;">
                        <h2 class="text-lg font-semibold text-ink">รายการสินค้า</h2>
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
                                    @foreach ($productionOrder->quotation->items as $item)
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
                                        <td colspan="3" class="px-3 py-4 text-right font-semibold text-pine-700">ยอดรวมทั้งหมด</td>
                                        <td class="px-3 py-4 text-right text-xl font-semibold text-ink">฿{{ number_format((float) $productionOrder->quotation->subtotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </section>
                </div>

                <aside class="min-w-0 space-y-6" style="min-width: 0; max-width: 100%;">
                    <section class="box-border max-w-full rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="box-sizing: border-box; max-width: 100%;">
                        <h2 class="text-lg font-semibold text-ink">เปลี่ยนสถานะงานผลิต</h2>
                        <p class="mt-1 text-sm text-pine-700">ย้ายใบสั่งผลิตไปตามขั้นตอนการผลิตเฟอร์นิเจอร์</p>

                        <div class="mt-5 grid gap-2">
                            @foreach ($stages as $value => $label)
                                <form action="{{ route('admin.production.status', $productionOrder) }}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $value }}">
                                    <button class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm font-semibold ring-1 ring-inset {{ $productionOrder->status === $value ? 'bg-pine-100 text-pine-700 ring-pine-600/20' : 'bg-white text-pine-700 ring-pine-200 hover:bg-pine-50' }}">
                                        <span>{{ $label }}</span>
                                        @if ($productionOrder->status === $value)
                                            <span class="text-xs">ปัจจุบัน</span>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>

                        @if ($nextMove)
                            <form action="{{ route('admin.production.status', $productionOrder) }}" method="post" class="mt-5">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nextMove['next'] }}">
                                <button class="w-full rounded-md bg-pine-700 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">{{ $nextMove['label'] }}</button>
                            </form>
                        @else
                            <div class="mt-5 rounded-md bg-green-50 p-4 text-center text-sm font-semibold text-green-700 ring-1 ring-green-600/20">ส่งมอบแล้ว</div>
                        @endif
                    </section>

                    <form action="{{ route('admin.production.craftsmen', $productionOrder) }}" method="post" class="box-border max-w-full rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="box-sizing: border-box; max-width: 100%;">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-lg font-semibold text-ink">ช่างผู้รับผิดชอบและหมายเหตุ</h2>
                        <div class="mt-5 space-y-3">
                            @forelse ($craftsmen as $craftsman)
                                <label class="flex items-center gap-3 rounded-md bg-pine-50 p-3 text-sm font-semibold text-ink">
                                    <input type="checkbox" name="craftsman_ids[]" value="{{ $craftsman->id }}" @checked($productionOrder->craftsmen->contains($craftsman)) class="rounded border-pine-300">
                                    <span>{{ $craftsman->name }}</span>
                                    @if ($craftsman->phone)
                                        <span class="text-xs font-normal text-pine-700">{{ $craftsman->phone }}</span>
                                    @endif
                                </label>
                            @empty
                                <p class="rounded-md border border-dashed border-pine-300 p-4 text-sm text-pine-700">ยังไม่ได้ระบุช่าง เพิ่มรายชื่อด้านล่างได้</p>
                            @endforelse
                        </div>

                        <div class="mt-5 grid gap-3">
                            <label>
                                <span class="text-sm font-semibold text-ink">วันนัดส่งมอบ</span>
                                <input name="delivery_date" type="date" value="{{ old('delivery_date', $productionOrder->delivery_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">วันนัดติดตั้ง</span>
                                <input name="installation_date" type="date" value="{{ old('installation_date', $productionOrder->installation_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">สถานะติดตั้ง</span>
                                <select name="installation_status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                    @foreach ($installationStatuses as $value => $label)
                                        <option value="{{ $value }}" @selected(old('installation_status', $productionOrder->installation_status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">ค่าจัดส่ง/ติดตั้ง</span>
                                <input name="delivery_cost" type="number" min="0" step="0.01" value="{{ old('delivery_cost', $productionOrder->delivery_cost) }}" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">ที่อยู่จัดส่ง/ติดตั้ง</span>
                                <textarea name="delivery_address" rows="3" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">{{ old('delivery_address', $productionOrder->delivery_address ?: $productionOrder->lead->province) }}</textarea>
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">เพิ่มชื่อช่าง</span>
                                <input name="new_craftsman_name" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">เบอร์โทรช่าง</span>
                                <input name="new_craftsman_phone" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            </label>
                            <label>
                                <span class="text-sm font-semibold text-ink">หมายเหตุ</span>
                                <textarea name="notes" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">{{ old('notes', $productionOrder->notes) }}</textarea>
                            </label>
                        </div>
                        <button class="mt-5 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกรายละเอียดงานผลิต</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
@endsection
