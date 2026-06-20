<?php
    $maxLead = max(1, $rows->max(fn ($row) => $row['website'] + $row['facebook'] + $row['line']) ?? 1);
    $maxForecast = max(1, $rows->max('forecast') ?? 1);
?>

<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-semibold text-pine-500">Marketing Analytics</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">วิเคราะห์ลีดและยอดขายล่วงหน้า</h1>
                <p class="mt-2 text-sm text-pine-700">ภาพรวมลีดรายเดือน แยกตาม Website, Facebook และ LINE OA พร้อม Conversion Rate และ Sales Forecast</p>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-3">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200 xl:col-span-2">
                    <h2 class="text-lg font-semibold text-ink">Leads by Channel</h2>
                    <div class="mt-6 space-y-5">
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $total = $row['website'] + $row['facebook'] + $row['line'];
                            ?>
                            <div>
                                <div class="mb-2 flex justify-between gap-3 text-sm">
                                    <span class="font-semibold text-ink"><?php echo e($row['month']); ?></span>
                                    <span class="text-pine-700"><?php echo e(number_format($total)); ?> leads</span>
                                </div>
                                <div class="flex h-4 overflow-hidden rounded-full bg-pine-100">
                                    <div class="bg-blue-500" style="width: <?php echo e(($row['website'] / $maxLead) * 100); ?>%"></div>
                                    <div class="bg-indigo-500" style="width: <?php echo e(($row['facebook'] / $maxLead) * 100); ?>%"></div>
                                    <div class="bg-emerald-500" style="width: <?php echo e(($row['line'] / $maxLead) * 100); ?>%"></div>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-3 text-xs text-pine-700">
                                    <span>Website <?php echo e($row['website']); ?></span>
                                    <span>Facebook <?php echo e($row['facebook']); ?></span>
                                    <span>LINE OA <?php echo e($row['line']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">Conversion Rate</h2>
                    <div class="mt-6 space-y-4">
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700"><?php echo e($row['month']); ?></span><span class="font-semibold text-ink"><?php echo e(number_format($row['conversion'], 1)); ?>%</span></div>
                                <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-emerald-500" style="width: <?php echo e(max(4, min(100, $row['conversion']))); ?>%"></div></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>

            <section class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">Sales Forecast</h2>
                <div class="mt-6 space-y-4">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="mb-1 flex justify-between gap-3 text-sm"><span class="font-medium text-pine-700"><?php echo e($row['month']); ?></span><span class="font-semibold text-ink">฿<?php echo e(number_format($row['forecast'], 2)); ?></span></div>
                            <div class="h-3 rounded-full bg-pine-100"><div class="h-3 rounded-full bg-amber-500" style="width: <?php echo e(max(4, ($row['forecast'] / $maxForecast) * 100)); ?>%"></div></div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Marketing Analytics | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views/admin/marketing/analytics.blade.php ENDPATH**/ ?>