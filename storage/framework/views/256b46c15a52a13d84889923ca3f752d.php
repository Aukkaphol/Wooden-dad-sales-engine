<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="<?php echo e(route('admin.purchase.index')); ?>" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($pr->pr_number); ?></h1>
                <p class="mt-2 text-sm text-pine-700"><?php echo e($pr->request_date->format('d/m/Y')); ?> · <?php echo e($pr->status_label); ?></p>
            </div>
            <a href="<?php echo e(route('admin.purchase.po.create', ['pr' => $pr->id])); ?>" class="w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white">สร้าง PO</a>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-[1fr_300px]">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">รายการวัตถุดิบที่ขอซื้อ</h2>
                <div class="mt-5 space-y-3">
                    <?php $__currentLoopData = $pr->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-md bg-pine-50 p-4">
                            <div class="flex justify-between gap-3">
                                <p class="font-semibold text-ink"><?php echo e($item->material->name); ?></p>
                                <p class="font-semibold text-pine-700"><?php echo e(number_format((float) $item->quantity, 3)); ?> <?php echo e($item->unit); ?></p>
                            </div>
                            <p class="mt-1 text-sm text-pine-700"><?php echo e($item->reason ?: $pr->reason); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <dl class="mt-6 grid gap-4 text-sm md:grid-cols-2">
                    <div><dt class="font-medium text-pine-500">ผู้ขอซื้อ</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($pr->requested_by); ?></dd></div>
                    <div><dt class="font-medium text-pine-500">งานผลิตอ้างอิง</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($pr->productionOrder?->production_order_number ?? '-'); ?></dd></div>
                    <div class="md:col-span-2"><dt class="font-medium text-pine-500">เหตุผล</dt><dd class="mt-1 whitespace-pre-line font-semibold text-ink"><?php echo e($pr->reason ?: '-'); ?></dd></div>
                </dl>
            </section>

            <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">เปลี่ยนสถานะ PR</h2>
                <div class="mt-5 space-y-2">
                    <?php $__currentLoopData = \App\Models\PurchaseRequisition::STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <form method="post" action="<?php echo e(route('admin.purchase.pr.status', $pr)); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="status" value="<?php echo e($value); ?>">
                            <button class="w-full rounded-md px-3 py-2 text-left text-sm font-semibold ring-1 <?php echo e($pr->status === $value ? 'bg-pine-100 text-pine-700 ring-pine-200' : 'bg-white text-pine-700 ring-pine-200 hover:bg-pine-50'); ?>"><?php echo e($label); ?></button>
                        </form>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => $pr->pr_number.' | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\purchase\pr-show.blade.php ENDPATH**/ ?>