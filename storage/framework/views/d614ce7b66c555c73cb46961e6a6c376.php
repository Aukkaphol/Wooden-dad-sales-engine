<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <a href="<?php echo e(route('admin.quotations.show', $quotation)); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไปหน้ารายละเอียด</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">แก้ไขใบเสนอราคา <?php echo e($quotation->display_number); ?></h1>
                    <p class="mt-2 text-sm text-pine-700">ปรับรายการสินค้า ส่วนลด ค่าขนส่ง เงินมัดจำ และหมายเหตุ</p>
                </div>
            </div>

            <?php echo $__env->make('admin.quotations._form', [
                'action' => route('admin.quotations.update', $quotation),
                'method' => 'PUT',
                'submitLabel' => 'บันทึกการแก้ไข',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'แก้ไขใบเสนอราคา | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\quotations\edit.blade.php ENDPATH**/ ?>