<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">แคตตาล็อกสินค้า</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">สินค้า ต้นทุน และกำไรขั้นต้น</h1>
                    <p class="mt-2 max-w-3xl text-sm text-pine-700">รวม SKU รูปสินค้า หมวดหมู่ ราคาขาย ต้นทุน BOM และกำไรของสินค้าเฟอร์นิเจอร์ไม้สน</p>
                </div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">กลับแดชบอร์ด</a>
            </div>

            <div class="grid gap-4 lg:hidden">
                <?php $__empty_1 = true; $__currentLoopData = $productCosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($product = $cost['product']); ?>
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                        <div class="flex gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-md bg-pine-100 text-sm font-semibold text-pine-700">
                                <?php if($product->product_image): ?>
                                    <img src="<?php echo e(asset('storage/'.$product->product_image)); ?>" alt="<?php echo e($product->name); ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                    รูปสินค้า
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs font-semibold text-pine-500"><?php echo e($product->sku ?? '-'); ?></p>
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold <?php echo e($product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600'); ?>"><?php echo e($product->is_active ? 'เปิดขาย' : 'พักขาย'); ?></span>
                                </div>
                                <h2 class="mt-1 text-lg font-semibold text-ink"><?php echo e($product->name); ?></h2>
                                <p class="mt-1 text-sm text-pine-700"><?php echo e($product->category ?: 'ไม่ระบุหมวดหมู่'); ?></p>
                            </div>
                        </div>

                        <dl class="mt-5 grid gap-3 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-pine-700">ราคาขาย</dt><dd class="font-semibold text-ink">฿<?php echo e(number_format((float) $product->selling_price, 2)); ?></dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-pine-700">ต้นทุนวัสดุจาก BOM</dt><dd class="font-semibold text-ink">฿<?php echo e(number_format($cost['material_cost'], 2)); ?></dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-pine-700">ต้นทุนรวม</dt><dd class="font-semibold text-ink">฿<?php echo e(number_format($cost['unit_production_cost'], 2)); ?></dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-pine-700">กำไรขั้นต้น</dt><dd class="font-semibold text-emerald-700">฿<?php echo e(number_format($cost['profit_amount'], 2)); ?></dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-pine-700">มาร์จิ้น</dt><dd class="font-semibold text-ink"><?php echo e(number_format($cost['profit_percent'], 2)); ?>%</dd></div>
                        </dl>

                        <div class="mt-5 rounded-md bg-pine-50 p-4">
                            <p class="text-sm font-semibold text-ink">รายการ BOM</p>
                            <div class="mt-3 space-y-2 text-sm text-pine-700">
                                <?php $__empty_2 = true; $__currentLoopData = $product->bomItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bomItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <div class="flex justify-between gap-3">
                                        <span><?php echo e($bomItem->material->name); ?></span>
                                        <span class="font-semibold"><?php echo e(number_format((float) $bomItem->quantity, 3)); ?> <?php echo e($bomItem->material->unit); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <p>ยังไม่มี BOM สำหรับสินค้านี้</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="rounded-lg border border-dashed border-pine-300 bg-white p-8 text-center text-sm text-pine-700">ยังไม่มีสินค้าในระบบ</p>
                <?php endif; ?>
            </div>

            <section class="hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200 lg:block">
                <table class="min-w-full table-fixed divide-y divide-pine-200 text-sm">
                    <thead class="bg-pine-100 text-pine-700">
                        <tr>
                            <th class="w-20 px-3 py-2 text-left font-semibold">รูป</th>
                            <th class="w-28 px-3 py-2 text-left font-semibold">SKU</th>
                            <th class="px-3 py-2 text-left font-semibold">สินค้า</th>
                            <th class="w-32 px-3 py-2 text-left font-semibold">หมวดหมู่</th>
                            <th class="w-28 px-3 py-2 text-right font-semibold">ราคาขาย</th>
                            <th class="w-28 px-3 py-2 text-right font-semibold">ต้นทุนวัสดุ</th>
                            <th class="w-28 px-3 py-2 text-right font-semibold">ต้นทุนรวม</th>
                            <th class="w-28 px-3 py-2 text-right font-semibold">กำไรขั้นต้น</th>
                            <th class="w-24 px-3 py-2 text-right font-semibold">มาร์จิ้น</th>
                            <th class="w-24 px-3 py-2 text-center font-semibold">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pine-100">
                        <?php $__empty_1 = true; $__currentLoopData = $productCosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php ($product = $cost['product']); ?>
                            <tr class="align-top">
                                <td class="px-3 py-3">
                                    <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-md bg-pine-100 text-xs font-semibold text-pine-700">
                                        <?php if($product->product_image): ?>
                                            <img src="<?php echo e(asset('storage/'.$product->product_image)); ?>" alt="<?php echo e($product->name); ?>" class="h-full w-full object-cover">
                                        <?php else: ?>
                                            รูป
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-3 py-3 font-semibold text-pine-700"><?php echo e($product->sku ?? '-'); ?></td>
                                <td class="px-3 py-3">
                                    <p class="font-medium text-ink"><?php echo e($product->name); ?></p>
                                    <div class="mt-2 space-y-1 text-xs text-pine-700">
                                        <?php $__empty_2 = true; $__currentLoopData = $product->bomItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bomItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <p><?php echo e($bomItem->material->name); ?>: <?php echo e(number_format((float) $bomItem->quantity, 3)); ?> <?php echo e($bomItem->material->unit); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <p>ยังไม่มี BOM</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-pine-700"><?php echo e($product->category ?: '-'); ?></td>
                                <td class="px-3 py-3 text-right text-pine-700">฿<?php echo e(number_format((float) $product->selling_price, 2)); ?></td>
                                <td class="px-3 py-3 text-right text-pine-700">฿<?php echo e(number_format($cost['material_cost'], 2)); ?></td>
                                <td class="px-3 py-3 text-right font-semibold text-ink">฿<?php echo e(number_format($cost['unit_production_cost'], 2)); ?></td>
                                <td class="px-3 py-3 text-right font-semibold text-emerald-700">฿<?php echo e(number_format($cost['profit_amount'], 2)); ?></td>
                                <td class="px-3 py-3 text-right font-semibold text-ink"><?php echo e(number_format($cost['profit_percent'], 2)); ?>%</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo e($product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600'); ?>"><?php echo e($product->is_active ? 'เปิดขาย' : 'พักขาย'); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="10" class="px-3 py-8 text-center text-pine-700">ยังไม่มีสินค้าในระบบ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'แคตตาล็อกสินค้า | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\products\index.blade.php ENDPATH**/ ?>