<?php
    $monthFormatter = fn (string $month): string => \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
?>

<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">ตารางส่งมอบและติดตั้ง</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ปฏิทินนัดส่งงานและติดตั้ง</h1>
                    <p class="mt-2 max-w-3xl text-sm text-pine-700">ติดตามวันส่งมอบ วันติดตั้ง สถานะงาน และทีมช่างที่รับผิดชอบในแต่ละใบสั่งผลิต</p>
                </div>
                <a href="<?php echo e(route('admin.production.index')); ?>" class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">กลับคิวงานผลิต</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-[300px_1fr]">
                <aside class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">สรุปสถานะติดตั้ง</h2>
                    <div class="mt-5 space-y-3">
                        <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between gap-3 rounded-md bg-pine-50 p-3">
                                <span class="text-sm font-medium text-pine-700"><?php echo e($label); ?></span>
                                <span class="text-lg font-semibold text-ink"><?php echo e(number_format($orders->where('installation_status', $status)->count())); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </aside>

                <div class="space-y-6">
                    <?php $__empty_1 = true; $__currentLoopData = $ordersByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month => $monthOrders): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                            <div class="flex items-center justify-between gap-4">
                                <h2 class="text-lg font-semibold text-ink"><?php echo e($monthFormatter($month)); ?></h2>
                                <span class="rounded-full bg-pine-100 px-3 py-1 text-sm font-semibold text-pine-700"><?php echo e(number_format($monthOrders->count())); ?> งาน</span>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <?php $__currentLoopData = $monthOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($statusLabel = $statusLabels[$order->installation_status] ?? 'รอตรวจสอบสถานะ'); ?>
                                    <article class="rounded-lg border border-pine-200 bg-pine-50 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <a href="<?php echo e(route('admin.production.show', $order)); ?>" class="font-semibold text-ink hover:text-pine-700"><?php echo e($order->production_order_number); ?></a>
                                                <p class="mt-1 text-sm text-pine-700"><?php echo e($order->lead->name); ?> · <?php echo e($order->lead->phone); ?></p>
                                            </div>
                                            <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-pine-700 ring-1 ring-pine-200"><?php echo e($statusLabel); ?></span>
                                        </div>

                                        <dl class="mt-4 space-y-2 text-sm">
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-pine-700">วันส่งมอบ</dt>
                                                <dd class="font-semibold text-ink"><?php echo e($order->delivery_date?->format('d/m/Y') ?? '-'); ?></dd>
                                            </div>
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-pine-700">วันติดตั้ง</dt>
                                                <dd class="font-semibold text-ink"><?php echo e($order->installation_date?->format('d/m/Y') ?? '-'); ?></dd>
                                            </div>
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-pine-700">จังหวัด</dt>
                                                <dd class="font-semibold text-ink"><?php echo e($order->lead->province); ?></dd>
                                            </div>
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-pine-700">ช่าง</dt>
                                                <dd class="text-right font-semibold text-ink"><?php echo e($order->craftsmen->pluck('name')->implode(', ') ?: '-'); ?></dd>
                                            </div>
                                        </dl>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </section>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <section class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-pine-200">
                            <h2 class="text-lg font-semibold text-ink">ยังไม่มีนัดส่งมอบหรือติดตั้ง</h2>
                            <p class="mt-2 text-sm text-pine-700">เมื่อเพิ่มวันส่งมอบหรือวันติดตั้งในใบสั่งผลิต งานจะแสดงในปฏิทินนี้</p>
                        </section>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'ตารางส่งมอบและติดตั้ง | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\installation-schedule\index.blade.php ENDPATH**/ ?>