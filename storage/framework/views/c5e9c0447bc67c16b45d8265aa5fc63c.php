<?php
    $selectedLead = old('lead_id', $quotation->lead_id ?? $lead?->id);
    $leadOptions = $leads->map(fn ($row) => [
        'id' => $row->id,
        'name' => $row->name,
        'phone' => $row->phone,
        'province' => $row->province,
        'budget' => $row->budget,
        'room_width' => $row->room_width,
        'room_length' => $row->room_length,
    ])->values();
    $defaultItems = $quotation?->items?->map(fn ($item) => [
        'item_name' => $item->display_name,
        'description' => $item->description,
        'qty' => $item->display_quantity,
        'unit' => $item->unit ?: 'ชุด',
        'unit_price' => $item->unit_price,
    ])->values()->all() ?: [['item_name' => 'Bedroom Set M', 'description' => '', 'qty' => 1, 'unit' => 'ชุด', 'unit_price' => 0]];
    $formItems = old('items', $defaultItems);
?>

<?php if($errors->any()): ?>
    <div class="mb-6 rounded-lg bg-rose-50 p-4 text-sm text-rose-800 ring-1 ring-rose-200">
        <p class="font-semibold">กรุณาตรวจสอบข้อมูลอีกครั้ง</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo e($action); ?>" method="post" class="space-y-6" data-quotation-form>
    <?php echo csrf_field(); ?>
    <?php if($method !== 'POST'): ?>
        <?php echo method_field($method); ?>
    <?php endif; ?>

    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200 sm:p-6">
        <div class="grid gap-5 lg:grid-cols-3">
            <label class="lg:col-span-1">
                <span class="text-sm font-semibold text-ink">เลือก Lead</span>
                <select name="lead_id" required data-lead-select class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    <option value="">เลือก Lead</option>
                    <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($row->id); ?>" <?php if((string) $selectedLead === (string) $row->id): echo 'selected'; endif; ?>><?php echo e($row->name); ?> | <?php echo e($row->phone); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <label>
                <span class="text-sm font-semibold text-ink">ชื่อลูกค้า</span>
                <input name="customer_name" value="<?php echo e(old('customer_name', $quotation->customer_name ?? $lead?->name)); ?>" required data-customer-name class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
            </label>
            <label>
                <span class="text-sm font-semibold text-ink">เบอร์โทร</span>
                <input name="phone" value="<?php echo e(old('phone', $quotation->phone ?? $lead?->phone)); ?>" required data-phone class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
            </label>
            <label>
                <span class="text-sm font-semibold text-ink">จังหวัด</span>
                <input name="province" value="<?php echo e(old('province', $quotation->province ?? $lead?->province)); ?>" required data-province class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
            </label>
            <label>
                <span class="text-sm font-semibold text-ink">ชื่องาน / โครงการ</span>
                <input name="project_name" value="<?php echo e(old('project_name', $quotation->project_name ?? 'เฟอร์นิเจอร์ไม้สนสั่งทำ')); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
            </label>
            <label>
                <span class="text-sm font-semibold text-ink">สถานะ</span>
                <select name="status" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(old('status', $quotation->status ?? 'draft') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
        </div>
        <div class="mt-5 grid gap-3 rounded-xl bg-pine-50 p-4 text-sm text-pine-700 sm:grid-cols-3">
            <p>งบประมาณ: <span class="font-semibold text-ink" data-budget><?php echo e($lead?->budget ?: '-'); ?></span></p>
            <p>กว้าง: <span class="font-semibold text-ink" data-room-width><?php echo e($lead?->room_width ?: '-'); ?></span></p>
            <p>ยาว: <span class="font-semibold text-ink" data-room-length><?php echo e($lead?->room_length ?: '-'); ?></span></p>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-ink">รายการสินค้า</h2>
                <p class="mt-1 text-sm text-pine-700">ระบบคำนวณ Amount และยอดรวมให้อัตโนมัติ</p>
            </div>
            <button type="button" data-add-row class="rounded-xl bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">เพิ่มรายการ</button>
        </div>

        <div class="mt-5 space-y-4" data-items>
            <?php $__currentLoopData = $formItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="grid gap-3 rounded-2xl bg-pine-50 p-4 lg:grid-cols-[1.1fr_1.2fr_90px_90px_130px_130px_auto]" data-item-row>
                    <label>
                        <span class="text-xs font-semibold text-pine-700">Item</span>
                        <input name="items[<?php echo e($index); ?>][item_name]" value="<?php echo e($item['item_name'] ?? ''); ?>" required class="mt-1 w-full rounded-xl border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-xs font-semibold text-pine-700">Description</span>
                        <input name="items[<?php echo e($index); ?>][description]" value="<?php echo e($item['description'] ?? ''); ?>" class="mt-1 w-full rounded-xl border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-xs font-semibold text-pine-700">Qty</span>
                        <input name="items[<?php echo e($index); ?>][qty]" type="number" step="0.01" min="0.01" value="<?php echo e($item['qty'] ?? 1); ?>" required data-qty class="mt-1 w-full rounded-xl border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-xs font-semibold text-pine-700">Unit</span>
                        <input name="items[<?php echo e($index); ?>][unit]" value="<?php echo e($item['unit'] ?? 'ชุด'); ?>" required class="mt-1 w-full rounded-xl border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-xs font-semibold text-pine-700">Unit Price</span>
                        <input name="items[<?php echo e($index); ?>][unit_price]" type="number" step="0.01" min="0" value="<?php echo e($item['unit_price'] ?? 0); ?>" required data-unit-price class="mt-1 w-full rounded-xl border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <div>
                        <span class="text-xs font-semibold text-pine-700">Amount</span>
                        <p class="mt-1 rounded-xl bg-white px-3 py-2.5 text-right text-sm font-semibold text-ink ring-1 ring-pine-200" data-line-total>0.00</p>
                    </div>
                    <button type="button" data-remove-row class="self-end rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-pine-200">ลบ</button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200 sm:p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <label>
                    <span class="text-sm font-semibold text-ink">ใช้ได้ถึงวันที่</span>
                    <input name="valid_until" type="date" value="<?php echo e(old('valid_until', optional($quotation?->valid_until)->format('Y-m-d'))); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                </label>
                <label>
                    <span class="text-sm font-semibold text-ink">หมายเหตุ</span>
                    <input name="remark" value="<?php echo e(old('remark', $quotation->remark ?? $quotation->notes ?? '')); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                </label>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200 sm:p-6">
            <h2 class="text-lg font-semibold text-ink">สรุปยอด</h2>
            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-pine-700">Subtotal</span>
                    <span class="font-semibold text-ink">฿<span data-subtotal>0.00</span></span>
                </div>
                <label class="flex items-center justify-between gap-4">
                    <span class="text-sm text-pine-700">Discount</span>
                    <input name="discount" type="number" step="0.01" min="0" value="<?php echo e(old('discount', $quotation->discount ?? 0)); ?>" data-discount class="w-36 rounded-xl border-0 bg-pine-50 px-3 py-2 text-right text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                </label>
                <label class="flex items-center justify-between gap-4">
                    <span class="text-sm text-pine-700">Shipping</span>
                    <input name="shipping_cost" type="number" step="0.01" min="0" value="<?php echo e(old('shipping_cost', $quotation->shipping_cost ?? 0)); ?>" data-shipping class="w-36 rounded-xl border-0 bg-pine-50 px-3 py-2 text-right text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                </label>
                <label class="flex items-center justify-between gap-4">
                    <span class="text-sm text-pine-700">Deposit</span>
                    <input name="deposit_amount" type="number" step="0.01" min="0" value="<?php echo e(old('deposit_amount', $quotation->deposit_amount ?? 0)); ?>" data-deposit class="w-36 rounded-xl border-0 bg-pine-50 px-3 py-2 text-right text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                </label>
            </div>
            <div class="mt-5 border-t border-pine-100 pt-4">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-ink">Grand Total</span>
                    <span class="text-2xl font-semibold text-ink">฿<span data-grand-total>0.00</span></span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-sm text-pine-700">Balance</span>
                    <span class="font-semibold text-pine-700">฿<span data-balance>0.00</span></span>
                </div>
            </div>
            <button class="mt-6 w-full rounded-xl bg-pine-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-pine-500"><?php echo e($submitLabel); ?></button>
        </div>
    </section>
</form>

<script>
    (() => {
        const form = document.querySelector('[data-quotation-form]');
        if (!form) return;

        const leads = <?php echo json_encode($leadOptions, 15, 512) ?>;
        const leadSelect = form.querySelector('[data-lead-select]');
        const items = form.querySelector('[data-items]');
        let nextIndex = items.querySelectorAll('[data-item-row]').length;
        const money = (value) => Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const calculate = () => {
            let subtotal = 0;
            items.querySelectorAll('[data-item-row]').forEach((row) => {
                const qty = parseFloat(row.querySelector('[data-qty]')?.value || 0);
                const unitPrice = parseFloat(row.querySelector('[data-unit-price]')?.value || 0);
                const total = qty * unitPrice;
                subtotal += total;
                row.querySelector('[data-line-total]').textContent = money(total);
            });

            const discount = parseFloat(form.querySelector('[data-discount]')?.value || 0);
            const shipping = parseFloat(form.querySelector('[data-shipping]')?.value || 0);
            const deposit = parseFloat(form.querySelector('[data-deposit]')?.value || 0);
            const grand = Math.max(0, subtotal - discount + shipping);
            const balance = Math.max(0, grand - deposit);

            form.querySelector('[data-subtotal]').textContent = money(subtotal);
            form.querySelector('[data-grand-total]').textContent = money(grand);
            form.querySelector('[data-balance]').textContent = money(balance);
        };

        leadSelect?.addEventListener('change', () => {
            const lead = leads.find((row) => String(row.id) === String(leadSelect.value));
            if (!lead) return;
            form.querySelector('[data-customer-name]').value = lead.name || '';
            form.querySelector('[data-phone]').value = lead.phone || '';
            form.querySelector('[data-province]').value = lead.province || '';
            form.querySelector('[data-budget]').textContent = lead.budget || '-';
            form.querySelector('[data-room-width]').textContent = lead.room_width || '-';
            form.querySelector('[data-room-length]').textContent = lead.room_length || '-';
        });

        form.addEventListener('input', calculate);
        form.querySelector('[data-add-row]')?.addEventListener('click', () => {
            const row = items.querySelector('[data-item-row]').cloneNode(true);
            row.querySelectorAll('input').forEach((input) => {
                input.name = input.name.replace(/items\[\d+\]/, `items[${nextIndex}]`);
                if (input.matches('[data-qty]')) input.value = '1';
                else if (input.matches('[data-unit-price]')) input.value = '0';
                else if (input.name.endsWith('[unit]')) input.value = 'ชุด';
                else input.value = '';
            });
            row.querySelector('[data-line-total]').textContent = '0.00';
            items.appendChild(row);
            nextIndex++;
            calculate();
        });
        items.addEventListener('click', (event) => {
            if (event.target.matches('[data-remove-row]') && items.querySelectorAll('[data-item-row]').length > 1) {
                event.target.closest('[data-item-row]').remove();
                calculate();
            }
        });

        calculate();
    })();
</script>
<?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\quotations\_form.blade.php ENDPATH**/ ?>