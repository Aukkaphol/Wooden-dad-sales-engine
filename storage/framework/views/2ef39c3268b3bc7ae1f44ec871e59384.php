<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับทะเบียนผู้จำหน่าย</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($supplier->supplier_name); ?></h1>
                <p class="mt-2 text-sm text-pine-700"><?php echo e($supplier->supplier_code); ?> · <?php echo e($supplier->is_active ? 'ใช้งาน' : 'พักใช้งาน'); ?></p>
            </div>
            <a href="<?php echo e(route('admin.suppliers.edit', $supplier)); ?>" class="w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">แก้ไขผู้จำหน่าย</a>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-[1fr_1.2fr]">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ข้อมูลผู้จำหน่าย</h2>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div><dt class="font-medium text-pine-500">ผู้ติดต่อ</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($supplier->contact_person ?: '-'); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">โทรศัพท์</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($supplier->phone ?: '-'); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">LINE ID</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($supplier->line_id ?: '-'); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">อีเมล</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($supplier->email ?: '-'); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">เลขภาษี</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($supplier->tax_id ?: '-'); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">ที่อยู่</dt><dd class="mt-1 whitespace-pre-line font-semibold text-ink"><?php echo e($supplier->address ?: '-'); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">หมายเหตุ</dt><dd class="mt-1 whitespace-pre-line font-semibold text-ink"><?php echo e($supplier->notes ?: '-'); ?></dd></div>
                </dl>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">ประวัติใบสั่งซื้อ</h2>
                <div class="mt-5 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $supplier->purchaseOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('admin.purchase.po.show', $po)); ?>" class="block rounded-md bg-pine-50 p-4 hover:bg-pine-100">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-ink"><?php echo e($po->po_number); ?></p>
                                <p class="text-sm font-semibold text-pine-700">฿<?php echo e(number_format((float) $po->total_cost, 2)); ?></p>
                            </div>
                            <p class="mt-1 text-sm text-pine-700"><?php echo e($po->order_date?->format('d/m/Y')); ?> · <?php echo e($po->status_label); ?></p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="rounded-md border border-dashed border-pine-300 p-6 text-center text-sm text-pine-700">ยังไม่มีใบสั่งซื้อจากผู้จำหน่ายนี้</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => $supplier->supplier_name.' | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\suppliers\show.blade.php ENDPATH**/ ?>