<?php ($chartMax = max(1, $purchaseByMonth->max('value') ?? 1)); ?>

<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">Purchase & Procurement</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">ระบบจัดซื้อวัตถุดิบ</h1>
                <p class="mt-2 text-sm text-pine-700">เชื่อมคลังวัสดุ BOM งานผลิต ใบขอซื้อ ใบสั่งซื้อ และการรับสินค้าเข้าคลัง</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.purchase.pr.create')); ?>" class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้าง PR</a>
                <a href="<?php echo e(route('admin.purchase.po.create')); ?>" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">สร้าง PO</a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">PR เปิดอยู่</dt><dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($metrics['open_pr'])); ?></dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">PO เปิดอยู่</dt><dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($metrics['open_po'])); ?></dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">รอรับสินค้า</dt><dd class="mt-2 text-3xl font-semibold text-amber-700"><?php echo e(number_format($metrics['waiting_receipts'])); ?></dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">วัตถุดิบต่ำกว่าขั้นต่ำ</dt><dd class="mt-2 text-3xl font-semibold text-rose-700"><?php echo e(number_format($metrics['low_stock'])); ?></dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">ยอดซื้อเดือนนี้</dt><dd class="mt-2 text-2xl font-semibold text-ink">฿<?php echo e(number_format($metrics['purchase_value_this_month'], 2)); ?></dd></div>
            <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200"><dt class="text-sm text-pine-700">ผู้จำหน่าย</dt><dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($metrics['supplier_count'])); ?></dd></div>
        </dl>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ยอดจัดซื้อรายเดือน</h2>
                <div class="mt-5 space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $purchaseByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div>
                            <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700"><?php echo e($row->month); ?></span><span class="font-semibold text-ink">฿<?php echo e(number_format((float) $row->value, 2)); ?></span></div>
                            <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-amber-600" style="width: <?php echo e(max(6, ((float) $row->value / $chartMax) * 100)); ?>%"></div></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มียอดจัดซื้อ</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ข้อเสนอซื้อจาก Low Stock</h2>
                <div class="mt-5 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $lowStockSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="rounded-md bg-rose-50 p-4 ring-1 ring-rose-100">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-ink"><?php echo e($row['material']->name); ?></p>
                                    <p class="text-sm text-rose-700">คงเหลือ <?php echo e(number_format($row['current'], 3)); ?> / ขั้นต่ำ <?php echo e(number_format($row['minimum'], 3)); ?> <?php echo e($row['material']->unit); ?></p>
                                    <p class="mt-1 text-sm font-semibold text-rose-800">แนะนำซื้อ <?php echo e(number_format($row['suggested_quantity'], 3)); ?> <?php echo e($row['material']->unit); ?></p>
                                </div>
                                <form method="post" action="<?php echo e(route('admin.purchase.auto-pr.material', $row['material'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="rounded-md bg-pine-700 px-3 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้าง PR อัตโนมัติ</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีวัตถุดิบต่ำกว่าขั้นต่ำ</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-2">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ใบขอซื้อล่าสุด</h2>
                <div class="mt-5 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $purchaseRequisitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('admin.purchase.pr.show', $pr)); ?>" class="block rounded-md bg-pine-50 p-4 hover:bg-pine-100">
                            <div class="flex justify-between gap-3"><p class="font-semibold text-ink"><?php echo e($pr->pr_number); ?></p><p class="text-sm font-semibold text-pine-700"><?php echo e($pr->status_label); ?></p></div>
                            <p class="mt-1 text-sm text-pine-700"><?php echo e($pr->requested_by); ?> · <?php echo e($pr->request_date->format('d/m/Y')); ?></p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มี PR</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ใบสั่งซื้อล่าสุด</h2>
                <div class="mt-5 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('admin.purchase.po.show', $po)); ?>" class="block rounded-md bg-pine-50 p-4 hover:bg-pine-100">
                            <div class="flex justify-between gap-3"><p class="font-semibold text-ink"><?php echo e($po->po_number); ?></p><p class="text-sm font-semibold text-pine-700">฿<?php echo e(number_format((float) $po->total_cost, 2)); ?></p></div>
                            <p class="mt-1 text-sm text-pine-700"><?php echo e($po->supplier->supplier_name); ?> · <?php echo e($po->status_label); ?></p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มี PO</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <h2 class="text-lg font-semibold text-ink">รายงานจัดซื้อ</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <?php $__currentLoopData = ['purchase-summary' => 'สรุปการจัดซื้อ', 'supplier-summary' => 'สรุปตามผู้จำหน่าย', 'material-consumption' => 'การใช้วัสดุ', 'low-stock' => 'วัตถุดิบใกล้หมด', 'outstanding-po' => 'PO ค้างรับ']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-md bg-pine-50 p-4">
                        <p class="font-semibold text-ink"><?php echo e($label); ?></p>
                        <div class="mt-3 flex gap-2">
                            <a href="<?php echo e(route('admin.purchase.reports.export', [$type, 'excel'])); ?>" class="rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-pine-700 ring-1 ring-pine-200">Excel</a>
                            <a href="<?php echo e(route('admin.purchase.reports.export', [$type, 'pdf'])); ?>" class="rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-pine-700 ring-1 ring-pine-200">PDF</a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'จัดซื้อ | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\purchase\index.blade.php ENDPATH**/ ?>