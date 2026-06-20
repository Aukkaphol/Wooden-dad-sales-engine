<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div>
                <p class="text-sm font-semibold text-pine-500">Marketing Center</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">Campaigns</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-pine-700">พื้นที่เตรียมจัดการแคมเปญจาก Website, Facebook, LINE OA, TikTok Lead Form และ Google Analytics ในอนาคต</p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel => $total): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <p class="text-sm font-medium text-pine-700"><?php echo e($channel); ?></p>
                        <p class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($total)); ?></p>
                        <p class="mt-2 text-sm text-pine-600">ลีดสะสมจากช่องทางนี้</p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="mt-8 rounded-2xl border border-dashed border-pine-300 bg-white p-8 text-center text-pine-700">
                ระบบ Campaign จะรองรับ UTM, แคมเปญโฆษณา และ Lead Form เพิ่มเติมโดยใช้โครงสร้างข้อมูลที่เตรียมไว้แล้ว
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Campaigns | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\marketing\campaigns.blade.php ENDPATH**/ ?>