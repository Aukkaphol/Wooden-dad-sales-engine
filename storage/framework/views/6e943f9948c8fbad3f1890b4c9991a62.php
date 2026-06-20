<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="<?php echo e(route('admin.settings.line.edit')); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับตั้งค่า LINE OA</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink">ประวัติการส่ง LINE Notification</h1>
                <p class="mt-2 text-sm text-pine-700">ตรวจสอบ event, channel, ผู้รับ, สถานะ และ error จาก LINE OA</p>
            </div>
        </div>

        <div class="grid gap-4 lg:hidden">
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-ink"><?php echo e($log->event); ?></p>
                            <p class="mt-1 text-sm text-pine-700"><?php echo e(ucfirst($log->channel)); ?> · <?php echo e($log->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold <?php echo e($log->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : ($log->status === 'failed' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')); ?>"><?php echo e($log->status); ?></span>
                    </div>
                    <p class="mt-3 text-sm text-pine-700">ผู้รับ: <?php echo e($log->recipient_id ?: '-'); ?></p>
                    <p class="mt-2 whitespace-pre-line rounded-md bg-pine-50 p-3 text-sm text-ink"><?php echo e($log->message); ?></p>
                    <?php if($log->error_message): ?>
                        <p class="mt-2 rounded-md bg-rose-50 p-3 text-sm text-rose-700"><?php echo e($log->error_message); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="rounded-lg bg-white p-8 text-center text-sm text-pine-700 ring-1 ring-pine-200">ยังไม่มีประวัติการส่ง Notification</p>
            <?php endif; ?>
        </div>

        <section class="hidden overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200 lg:block">
            <table class="min-w-full table-fixed divide-y divide-pine-200 text-sm">
                <thead class="bg-pine-100 text-pine-700">
                    <tr>
                        <th class="w-36 px-3 py-2 text-left font-semibold">เวลา</th>
                        <th class="w-44 px-3 py-2 text-left font-semibold">Event</th>
                        <th class="w-28 px-3 py-2 text-left font-semibold">Channel</th>
                        <th class="w-32 px-3 py-2 text-left font-semibold">สถานะ</th>
                        <th class="px-3 py-2 text-left font-semibold">ข้อความ / Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pine-100">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="align-top">
                            <td class="px-3 py-3 text-pine-700"><?php echo e($log->created_at->format('d/m/Y H:i')); ?></td>
                            <td class="px-3 py-3 font-semibold text-ink"><?php echo e($log->event); ?></td>
                            <td class="px-3 py-3 text-pine-700"><?php echo e(ucfirst($log->channel)); ?></td>
                            <td class="px-3 py-3"><span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo e($log->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : ($log->status === 'failed' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')); ?>"><?php echo e($log->status); ?></span></td>
                            <td class="px-3 py-3 text-pine-700">
                                <p class="line-clamp-3 whitespace-pre-line"><?php echo e($log->message); ?></p>
                                <?php if($log->error_message): ?>
                                    <p class="mt-2 rounded-md bg-rose-50 p-2 text-rose-700"><?php echo e($log->error_message); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-3 py-8 text-center text-pine-700">ยังไม่มีประวัติการส่ง Notification</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <div class="mt-6"><?php echo e($logs->links()); ?></div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'ประวัติ LINE Notification | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\settings\line-logs.blade.php ENDPATH**/ ?>