<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="<?php echo e(route('admin.purchase.index')); ?>" class="text-sm font-semibold text-pine-700">กลับระบบจัดซื้อ</a>
        <h1 class="mt-2 text-3xl font-semibold text-ink">สร้างใบขอซื้อภายใน</h1>
        <p class="mt-2 text-sm text-pine-700">เลขที่เอกสารตัวอย่าง: <?php echo e($prNumber); ?></p>

        <?php if($errors->any()): ?>
            <div class="mt-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo e(route('admin.purchase.pr.store')); ?>" class="mt-6 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <?php echo csrf_field(); ?>
            <div class="grid gap-4 md:grid-cols-2">
                <label><span class="text-sm font-semibold text-ink">วันที่ขอซื้อ</span><input type="date" name="request_date" value="<?php echo e(old('request_date', now()->format('Y-m-d'))); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">ผู้ขอซื้อ</span><input name="requested_by" value="<?php echo e(old('requested_by', auth()->user()->name ?? 'แอดมิน')); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">วัตถุดิบ</span><select name="material_id" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($material->id); ?>"><?php echo e($material->name); ?> (<?php echo e($material->unit); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label>
                <label><span class="text-sm font-semibold text-ink">จำนวน</span><input type="number" step="0.001" min="0.001" name="quantity" value="<?php echo e(old('quantity')); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">สถานะ</span><select name="status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><option value="draft">ฉบับร่าง</option><option value="waiting_approval" selected>รออนุมัติ</option><option value="approved">อนุมัติแล้ว</option></select></label>
                <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">เหตุผลการขอซื้อ</span><textarea name="reason" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('reason')); ?></textarea></label>
            </div>
            <button class="mt-6 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white">บันทึก PR</button>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'สร้าง PR | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\purchase\pr-form.blade.php ENDPATH**/ ?>