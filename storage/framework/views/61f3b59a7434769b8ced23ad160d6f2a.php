<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">คลังวัสดุและ BOM</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ควบคุมสต็อกวัสดุงานเฟอร์นิเจอร์</h1>
                    <p class="mt-2 text-sm text-pine-700">ควบคุมวัสดุ รับเข้า ปรับยอด ตัดใช้ และ BOM สำหรับงานผลิต</p>
                </div>
                <a href="<?php echo e(route('admin.production.index')); ?>" class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">คิวงานผลิต</a>
            </div>

            <?php if(session('success')): ?>
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">รายการวัสดุในคลัง</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($materials->count())); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">วัสดุใกล้หมด</dt>
                    <dd class="mt-2 text-3xl font-semibold text-rose-700"><?php echo e(number_format($lowStockMaterials->count())); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">รายการตัดใช้วัสดุ</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700"><?php echo e(number_format($usage->count())); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">มูลค่าวัสดุคงคลัง</dt>
                    <dd class="mt-2 text-3xl font-semibold text-emerald-700">฿<?php echo e(number_format($materialCost, 2)); ?></dd>
                </div>
            </dl>

            <?php if($lowStockMaterials->isNotEmpty()): ?>
                <section class="mt-8 rounded-lg bg-rose-50 p-5 ring-1 ring-rose-200">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-rose-900">แจ้งเตือนวัสดุใกล้หมด</h2>
                            <p class="mt-1 text-sm text-rose-700">วัสดุที่ต่ำกว่าหรือเท่ากับระดับขั้นต่ำที่กำหนด</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $lowStockMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-rose-700 ring-1 ring-rose-200">
                                    <?php echo e($material->name); ?>: <?php echo e(number_format((float) $material->current_stock, 3)); ?> <?php echo e($material->unit); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">สต็อกปัจจุบัน</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-pine-200 text-sm">
                            <thead class="bg-pine-100 text-pine-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">วัสดุ</th>
                                    <th class="px-3 py-2 text-right font-semibold">คงเหลือ</th>
                                    <th class="px-3 py-2 text-right font-semibold">จองผลิต</th>
                                    <th class="px-3 py-2 text-right font-semibold">พร้อมใช้</th>
                                    <th class="px-3 py-2 text-right font-semibold">ขั้นต่ำ</th>
                                    <th class="px-3 py-2 text-right font-semibold">ต้นทุน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pine-100">
                                <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e((float) $material->current_stock <= (float) $material->low_stock_level ? 'bg-rose-50/50' : ''); ?>">
                                        <td class="px-3 py-3 font-medium text-ink"><?php echo e($material->name); ?></td>
                                        <td class="px-3 py-3 text-right text-pine-700"><?php echo e(number_format((float) $material->current_stock, 3)); ?> <?php echo e($material->unit); ?></td>
                                        <td class="px-3 py-3 text-right text-pine-700"><?php echo e(number_format((float) $material->reserved_stock, 3)); ?></td>
                                        <td class="px-3 py-3 text-right font-semibold text-ink"><?php echo e(number_format((float) $material->current_stock - (float) $material->reserved_stock, 3)); ?></td>
                                        <td class="px-3 py-3 text-right text-pine-700"><?php echo e(number_format((float) $material->low_stock_level, 3)); ?></td>
                                        <td class="px-3 py-3 text-right font-semibold text-ink"><?php echo e(number_format((float) $material->unit_cost, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">รายการเคลื่อนไหวสต็อก</h2>
                    <div class="mt-5 space-y-5">
                        <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <form action="<?php echo e(route('admin.inventory.transactions.store', $material)); ?>" method="post" class="rounded-lg bg-pine-50 p-4">
                                <?php echo csrf_field(); ?>
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-ink"><?php echo e($material->name); ?></p>
                                        <p class="mt-1 text-xs text-pine-700">คงเหลือ <?php echo e(number_format((float) $material->current_stock, 3)); ?> <?php echo e($material->unit); ?></p>
                                    </div>
                                    <select name="type" class="rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                        <?php $__currentLoopData = $transactionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input name="quantity" type="number" step="0.001" placeholder="จำนวน" class="w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500 sm:w-28">
                                    <input name="unit_cost" type="number" step="0.01" min="0" value="<?php echo e($material->unit_cost); ?>" class="w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500 sm:w-28">
                                    <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">บันทึก</button>
                                </div>
                                <input name="notes" placeholder="หมายเหตุ" class="mt-3 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            </form>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-2">
                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">สูตรวัสดุการผลิต: เตียง 6 ฟุต</h2>
                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-pine-200 text-sm">
                            <thead class="bg-pine-100 text-pine-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">สินค้า</th>
                                    <th class="px-3 py-2 text-left font-semibold">วัสดุ</th>
                                    <th class="px-3 py-2 text-right font-semibold">ปริมาณใช้</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pine-100">
                                <?php $__currentLoopData = $bomItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bomItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-3 py-3 font-medium text-ink"><?php echo e($bomItem->product->name); ?></td>
                                        <td class="px-3 py-3 text-pine-700"><?php echo e($bomItem->material->name); ?></td>
                                        <td class="px-3 py-3 text-right font-semibold text-ink"><?php echo e(number_format((float) $bomItem->quantity, 3)); ?> <?php echo e($bomItem->material->unit); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">สรุปการใช้วัสดุ</h2>
                    <div class="mt-5 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $usage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="rounded-md bg-pine-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-ink"><?php echo e($row->material->name); ?></p>
                                    <p class="text-sm font-semibold text-pine-700">฿<?php echo e(number_format((float) $row->total_cost, 2)); ?></p>
                                </div>
                                <p class="mt-1 text-sm text-pine-700">ตัดใช้แล้ว <?php echo e(number_format((float) $row->total_quantity, 3)); ?> <?php echo e($row->material->unit); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีประวัติการใช้วัสดุ</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-3">
                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">คำนวณต้นทุนสินค้า</h2>
                    <div class="mt-5 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $productCosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="rounded-md bg-pine-50 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-ink"><?php echo e($cost['product']->name); ?></p>
                                    <p class="text-sm font-semibold text-pine-700">฿<?php echo e(number_format($cost['production_cost'], 2)); ?></p>
                                </div>
                                <dl class="mt-3 grid gap-2 text-sm">
                                    <div class="flex justify-between gap-3"><dt class="text-pine-700">ต้นทุนวัสดุจาก BOM</dt><dd class="font-semibold text-ink"><?php echo e(number_format($cost['material_cost'], 2)); ?></dd></div>
                                    <div class="flex justify-between gap-3"><dt class="text-pine-700">ค่าแรงผลิต</dt><dd class="font-semibold text-ink"><?php echo e(number_format($cost['labor_cost'], 2)); ?></dd></div>
                                    <div class="flex justify-between gap-3"><dt class="text-pine-700">ต้นทุนงานสี</dt><dd class="font-semibold text-ink"><?php echo e(number_format($cost['finishing_cost'], 2)); ?></dd></div>
                                    <div class="flex justify-between gap-3"><dt class="text-pine-700">ต้นทุนฮาร์ดแวร์</dt><dd class="font-semibold text-ink"><?php echo e(number_format($cost['hardware_cost'], 2)); ?></dd></div>
                                </dl>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีข้อมูลต้นทุนสินค้า</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">สินค้ากำไรสูงสุด</h2>
                    <div class="mt-5 space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $topProfitableProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="rounded-md bg-emerald-50 p-4 ring-1 ring-emerald-100">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-ink"><?php echo e($product['product_name']); ?></p>
                                    <p class="text-sm font-semibold text-emerald-700"><?php echo e(number_format($product['profit_percent'], 2)); ?>%</p>
                                </div>
                                <p class="mt-1 text-sm text-emerald-700">กำไรขั้นต้น ฿<?php echo e(number_format($product['gross_profit'], 2)); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีข้อมูลกำไรจากใบเสนอราคา</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                    <h2 class="text-lg font-semibold text-ink">สินค้ามาร์จิ้นต่ำ</h2>
                    <div class="mt-5 space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $topLowMarginProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="rounded-md bg-amber-50 p-4 ring-1 ring-amber-100">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-ink"><?php echo e($product['product_name']); ?></p>
                                    <p class="text-sm font-semibold text-amber-700"><?php echo e(number_format($product['profit_percent'], 2)); ?>%</p>
                                </div>
                                <p class="mt-1 text-sm text-amber-700">ต้นทุนผลิต ฿<?php echo e(number_format($product['production_cost'], 2)); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีข้อมูลสินค้ามาร์จิ้นต่ำ</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <section class="mt-8 min-w-0 max-w-full overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200" style="width: 100%; min-width: 0; max-width: 100%;">
                <h2 class="text-lg font-semibold text-ink">รายการสต็อกล่าสุด</h2>
                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-pine-200 text-sm">
                        <thead class="bg-pine-100 text-pine-700">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">วันที่</th>
                                <th class="px-3 py-2 text-left font-semibold">วัสดุ</th>
                                <th class="px-3 py-2 text-left font-semibold">ประเภท</th>
                                <th class="px-3 py-2 text-right font-semibold">จำนวน</th>
                                <th class="px-3 py-2 text-left font-semibold">งานผลิต</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pine-100">
                            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-3 py-3 text-pine-700"><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>
                                    <td class="px-3 py-3 font-medium text-ink"><?php echo e($transaction->material->name); ?></td>
                                    <td class="px-3 py-3 text-pine-700"><?php echo e($transaction->type); ?></td>
                                    <td class="px-3 py-3 text-right font-semibold text-ink"><?php echo e(number_format((float) $transaction->quantity, 3)); ?></td>
                                    <td class="px-3 py-3 text-pine-700"><?php echo e($transaction->productionOrder?->production_order_number ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-3 py-8 text-center text-pine-700">ยังไม่มีรายการเคลื่อนไหวสต็อก</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'คลังวัสดุและ BOM | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\inventory\index.blade.php ENDPATH**/ ?>