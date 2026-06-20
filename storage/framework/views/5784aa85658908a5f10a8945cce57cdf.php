<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="<?php echo e(route('admin.purchase.index')); ?>" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
            <h1 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($po->po_number); ?></h1>
            <p class="mt-2 text-sm text-pine-700"><?php echo e($po->supplier->supplier_name); ?> · <?php echo e($po->status_label); ?></p>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="space-y-6">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">ข้อมูลใบสั่งซื้อ</h2>
                    <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2">
                        <div><dt class="font-medium text-pine-500">วันที่สั่งซื้อ</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($po->order_date->format('d/m/Y')); ?></dd></div>
                        <div><dt class="font-medium text-pine-500">กำหนดส่ง</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($po->expected_delivery_date?->format('d/m/Y') ?? '-'); ?></dd></div>
                        <div><dt class="font-medium text-pine-500">อ้างอิง PR</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($po->purchaseRequisition?->pr_number ?? '-'); ?></dd></div>
                        <div><dt class="font-medium text-pine-500">ยอดรวม</dt><dd class="mt-1 font-semibold text-ink">฿<?php echo e(number_format((float) $po->total_cost, 2)); ?></dd></div>
                        <div class="md:col-span-2"><dt class="font-medium text-pine-500">หมายเหตุ</dt><dd class="mt-1 whitespace-pre-line font-semibold text-ink"><?php echo e($po->notes ?: '-'); ?></dd></div>
                    </dl>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">รายการวัตถุดิบ</h2>
                    <div class="mt-5 space-y-4">
                        <?php $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $remaining = max(0, (float) $item->quantity - (float) $item->received_quantity);
                            ?>
                            <div class="rounded-md bg-pine-50 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-ink"><?php echo e($item->material->name); ?></p>
                                        <p class="mt-1 text-sm text-pine-700">สั่งซื้อ <?php echo e(number_format((float) $item->quantity, 3)); ?> <?php echo e($item->unit); ?> · รับแล้ว <?php echo e(number_format((float) $item->received_quantity, 3)); ?> · ค้างรับ <?php echo e(number_format($remaining, 3)); ?></p>
                                        <p class="mt-1 text-sm font-semibold text-pine-700">฿<?php echo e(number_format((float) $item->unit_cost, 2)); ?> / <?php echo e($item->unit); ?></p>
                                    </div>
                                    <?php if($remaining > 0): ?>
                                        <form method="post" action="<?php echo e(route('admin.purchase.receive', $item)); ?>" class="grid gap-2 sm:w-52">
                                            <?php echo csrf_field(); ?>
                                            <input type="date" name="receive_date" value="<?php echo e(now()->format('Y-m-d')); ?>" class="rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                            <input type="number" step="0.001" min="0.001" max="<?php echo e($remaining); ?>" name="received_quantity" value="<?php echo e($remaining); ?>" class="rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                            <button class="rounded-md bg-pine-700 px-3 py-2 text-sm font-semibold text-white">รับเข้าคลัง</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">รับครบแล้ว</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>

            <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ประวัติรับสินค้า</h2>
                <div class="mt-5 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $po->receipts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="rounded-md bg-pine-50 p-4">
                            <p class="font-semibold text-ink"><?php echo e($receipt->material->name); ?></p>
                            <p class="mt-1 text-sm text-pine-700"><?php echo e($receipt->receive_date->format('d/m/Y')); ?> · รับ <?php echo e(number_format((float) $receipt->received_quantity, 3)); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีการรับสินค้า</p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => $po->po_number.' | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\purchase\po-show.blade.php ENDPATH**/ ?>