<?php $__env->startSection('content'); ?>
<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 print:hidden sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="<?php echo e(route('admin.purchase.index')); ?>" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
                <h1 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($title); ?></h1>
            </div>
            <button onclick="window.print()" class="w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white">พิมพ์ / บันทึก PDF</button>
        </div>

        <section class="overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200 print:shadow-none print:ring-0">
            <h2 class="text-xl font-semibold text-ink"><?php echo e(company()->display_name); ?></h2>
            <p class="mt-1 text-sm text-pine-700"><?php echo e($title); ?> · <?php echo e(now()->format('d/m/Y H:i')); ?></p>
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-pine-200 text-sm">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($index === 0): ?>
                            <thead class="bg-pine-100 text-pine-700"><tr><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><th class="px-3 py-2 text-left font-semibold"><?php echo e($cell); ?></th><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr></thead><tbody class="divide-y divide-pine-100">
                        <?php else: ?>
                            <tr><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><td class="px-3 py-3 text-pine-700"><?php echo e($cell); ?></td><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => $title.' | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\purchase\report.blade.php ENDPATH**/ ?>