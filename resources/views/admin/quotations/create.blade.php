@extends('layouts.app', ['title' => 'สร้างใบเสนอราคา | Wooden Dad Design'])

@section('content')
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.leads.show', $lead) }}" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปหน้าลูกค้า</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink">สร้างใบเสนอราคา</h1>
                <p class="mt-2 text-sm text-pine-700">ใบเสนอราคา Wooden Dad Design สำหรับ {{ $lead->name }}</p>
            </div>

            <form action="{{ route('admin.leads.quotations.store', $lead) }}" method="post" class="space-y-6">
                @csrf
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">ข้อมูลลูกค้า</h2>
                            <dl class="mt-4 space-y-2 text-sm text-pine-700">
                                <div class="flex justify-between gap-4"><dt>ชื่อ</dt><dd class="font-semibold text-ink">{{ $lead->name }}</dd></div>
                                <div class="flex justify-between gap-4"><dt>เบอร์โทร</dt><dd class="font-semibold text-ink">{{ $lead->phone }}</dd></div>
                                <div class="flex justify-between gap-4"><dt>จังหวัด</dt><dd class="font-semibold text-ink">{{ $lead->province }}</dd></div>
                            </dl>
                        </div>
                        <label>
                            <span class="text-sm font-semibold text-ink">สถานะใบเสนอราคา</span>
                            <select name="status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', 'draft') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">รายการสินค้าในใบเสนอราคา</h2>
                            <p class="mt-1 text-sm text-pine-700">ระบบคำนวณยอดรวมจากจำนวนและราคาต่อหน่วย</p>
                        </div>
                        <button type="button" data-add-row class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">เพิ่มรายการ</button>
                    </div>

                    <div class="mt-5 space-y-4" data-items>
                        @foreach (old('items', [['product_name' => 'Bedroom Set M', 'quantity' => 1, 'unit_price' => 0]]) as $index => $item)
                            <div class="grid gap-3 rounded-lg bg-pine-50 p-4 md:grid-cols-[1fr_110px_150px_150px_auto]" data-item-row>
                                <label>
                                    <span class="text-xs font-semibold text-pine-700">ชื่อสินค้า</span>
                                    <input name="items[{{ $index }}][product_name]" value="{{ $item['product_name'] ?? '' }}" required class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                </label>
                                <label>
                                    <span class="text-xs font-semibold text-pine-700">จำนวน</span>
                                    <input name="items[{{ $index }}][quantity]" type="number" step="0.01" min="0.01" value="{{ $item['quantity'] ?? 1 }}" required data-quantity class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                </label>
                                <label>
                                    <span class="text-xs font-semibold text-pine-700">ราคาต่อหน่วย</span>
                                    <input name="items[{{ $index }}][unit_price]" type="number" step="0.01" min="0" value="{{ $item['unit_price'] ?? 0 }}" required data-unit-price class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                </label>
                                <div>
                                    <span class="text-xs font-semibold text-pine-700">ยอดรวมรายการ</span>
                                    <p class="mt-1 rounded-md bg-white px-3 py-2 text-sm font-semibold text-ink ring-1 ring-pine-200" data-line-subtotal>0.00</p>
                                </div>
                                <button type="button" data-remove-row class="self-end rounded-md bg-white px-3 py-2 text-sm font-semibold text-rose-700 ring-1 ring-pine-200">ลบ</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 flex justify-end">
                        <div class="rounded-lg bg-pine-100 px-5 py-4 text-right">
                            <p class="text-sm text-pine-700">ยอดรวมใบเสนอราคา</p>
                            <p class="text-2xl font-semibold text-ink">฿<span data-total>0.00</span></p>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <label>
                        <span class="text-sm font-semibold text-ink">หมายเหตุ</span>
                        <textarea name="notes" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">{{ old('notes') }}</textarea>
                    </label>
                    <button class="mt-5 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">สร้างใบเสนอราคา</button>
                </section>
            </form>
        </div>
    </section>

    <script>
        (() => {
            const container = document.querySelector('[data-items]');
            const total = document.querySelector('[data-total]');
            const addButton = document.querySelector('[data-add-row]');
            let nextIndex = container?.querySelectorAll('[data-item-row]').length ?? 0;

            const format = (value) => Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const calculate = () => {
                let sum = 0;
                container.querySelectorAll('[data-item-row]').forEach((row) => {
                    const quantity = parseFloat(row.querySelector('[data-quantity]')?.value || 0);
                    const unitPrice = parseFloat(row.querySelector('[data-unit-price]')?.value || 0);
                    const subtotal = quantity * unitPrice;
                    sum += subtotal;
                    row.querySelector('[data-line-subtotal]').textContent = format(subtotal);
                });
                total.textContent = format(sum);
            };

            container.addEventListener('input', calculate);
            container.addEventListener('click', (event) => {
                if (event.target.matches('[data-remove-row]') && container.querySelectorAll('[data-item-row]').length > 1) {
                    event.target.closest('[data-item-row]').remove();
                    calculate();
                }
            });
            addButton.addEventListener('click', () => {
                const row = container.querySelector('[data-item-row]').cloneNode(true);
                row.querySelectorAll('input').forEach((input) => {
                    input.name = input.name.replace(/items\[\d+\]/, `items[${nextIndex}]`);
                    input.value = input.matches('[data-quantity]') ? '1' : '0';
                    if (!input.matches('[data-quantity], [data-unit-price]')) input.value = '';
                });
                row.querySelector('[data-line-subtotal]').textContent = '0.00';
                container.appendChild(row);
                nextIndex++;
                calculate();
            });
            calculate();
        })();
    </script>
@endsection
