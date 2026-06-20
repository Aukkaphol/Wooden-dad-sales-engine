<?php
    $leadMax = max(1, $leadGrowth->max('value') ?? 1);
    $sourceMax = max(1, $leadSourceMonths->max(fn ($row) => $row['website'] + $row['facebook'] + $row['line']) ?? 1);
    $conversionMax = max(1, $conversionByMonth->max('value') ?? 1);
    $forecastMax = max(1, $salesForecast->max('value') ?? 1);
?>

<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Wooden Dad Design Sales CRM</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ศูนย์รวมยอดขายและการตลาดทุกช่องทาง</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-pine-700">ติดตามลีดจาก Website, Facebook และ LINE OA พร้อมสถานะใบเสนอราคา งานผลิต และอัตราปิดการขายในหน้าเดียว</p>
                </div>
                <a href="<?php echo e(route('admin.leads.index')); ?>" class="inline-flex w-fit rounded-xl bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">เปิด CRM Pipeline</a>
            </div>

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Total Leads</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($metrics['total_leads'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Website Leads</dt>
                    <dd class="mt-2 text-3xl font-semibold text-blue-700"><?php echo e(number_format($metrics['website_leads'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Facebook Leads</dt>
                    <dd class="mt-2 text-3xl font-semibold text-indigo-700"><?php echo e(number_format($metrics['facebook_leads'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">LINE Leads</dt>
                    <dd class="mt-2 text-3xl font-semibold text-emerald-700"><?php echo e(number_format($metrics['line_leads'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Pending Quotations</dt>
                    <dd class="mt-2 text-3xl font-semibold text-amber-700"><?php echo e(number_format($metrics['pending_quotations'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Active Production</dt>
                    <dd class="mt-2 text-3xl font-semibold text-orange-700"><?php echo e(number_format($metrics['active_production'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Sales This Month</dt>
                    <dd class="mt-2 text-2xl font-semibold text-ink">฿<?php echo e(number_format($metrics['sales_this_month'], 2)); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Conversion Rate</dt>
                    <dd class="mt-2 text-3xl font-semibold text-green-700"><?php echo e(number_format($metrics['conversion_rate'], 1)); ?>%</dd>
                </div>
            </dl>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">Lead Growth</h2>
                            <p class="text-sm text-pine-700">จำนวนลีดรวมย้อนหลัง 12 เดือน</p>
                        </div>
                    </div>
                    <div class="mt-6 flex h-64 items-end gap-3 overflow-x-auto pb-2">
                        <?php $__currentLoopData = $leadGrowth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex min-w-14 flex-1 flex-col items-center gap-2">
                                <div class="flex h-44 w-full items-end rounded-xl bg-pine-50 px-2">
                                    <div class="w-full rounded-t-xl bg-pine-600" style="height: <?php echo e(max(6, ($row['value'] / $leadMax) * 100)); ?>%"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-ink"><?php echo e(number_format($row['value'])); ?></p>
                                    <p class="text-[11px] text-pine-600"><?php echo e($row['month']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">Conversion Rate</h2>
                    <p class="mt-1 text-sm text-pine-700">อัตราปิดการขายรายเดือน</p>
                    <div class="mt-6 space-y-4">
                        <?php $__currentLoopData = $conversionByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <div class="mb-1 flex justify-between gap-3 text-sm">
                                    <span class="font-medium text-pine-700"><?php echo e($row['month']); ?></span>
                                    <span class="font-semibold text-ink"><?php echo e(number_format($row['value'], 1)); ?>%</span>
                                </div>
                                <div class="h-3 rounded-full bg-pine-100">
                                    <div class="h-3 rounded-full bg-emerald-500" style="width: <?php echo e(min(100, max(4, ($row['value'] / $conversionMax) * 100))); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-2">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">Leads by Channel</h2>
                    <p class="mt-1 text-sm text-pine-700">แยกตาม Website, Facebook และ LINE OA</p>
                    <div class="mt-6 space-y-5">
                        <?php $__currentLoopData = $leadSourceMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $total = $row['website'] + $row['facebook'] + $row['line'];
                            ?>
                            <div>
                                <div class="mb-2 flex justify-between gap-3 text-sm">
                                    <span class="font-semibold text-ink"><?php echo e($row['month']); ?></span>
                                    <span class="text-pine-700"><?php echo e(number_format($total)); ?> leads</span>
                                </div>
                                <div class="flex h-4 overflow-hidden rounded-full bg-pine-100">
                                    <div class="bg-blue-500" style="width: <?php echo e($total > 0 ? ($row['website'] / $sourceMax) * 100 : 0); ?>%"></div>
                                    <div class="bg-indigo-500" style="width: <?php echo e($total > 0 ? ($row['facebook'] / $sourceMax) * 100 : 0); ?>%"></div>
                                    <div class="bg-emerald-500" style="width: <?php echo e($total > 0 ? ($row['line'] / $sourceMax) * 100 : 0); ?>%"></div>
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
                    <h2 class="text-lg font-semibold text-ink">Sales Forecast</h2>
                    <p class="mt-1 text-sm text-pine-700">มูลค่าใบเสนอราคาที่ยังรอปิดการขาย</p>
                    <div class="mt-6 space-y-4">
                        <?php $__currentLoopData = $salesForecast; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <div class="mb-1 flex justify-between gap-3 text-sm">
                                    <span class="font-medium text-pine-700"><?php echo e($row['month']); ?></span>
                                    <span class="font-semibold text-ink">฿<?php echo e(number_format($row['value'], 2)); ?></span>
                                </div>
                                <div class="h-3 rounded-full bg-pine-100">
                                    <div class="h-3 rounded-full bg-amber-500" style="width: <?php echo e(max(4, ($row['value'] / $forecastMax) * 100)); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'แดชบอร์ด Sales CRM | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>