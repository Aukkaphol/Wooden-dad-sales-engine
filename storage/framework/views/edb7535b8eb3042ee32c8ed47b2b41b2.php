<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับทะเบียนผู้จำหน่าย</a>
            <h1 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($supplier->exists ? 'แก้ไขผู้จำหน่าย' : 'เพิ่มผู้จำหน่าย'); ?></h1>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo e($supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store')); ?>" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <?php echo csrf_field(); ?>
            <?php if($supplier->exists): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="grid gap-4 md:grid-cols-2">
                <label><span class="text-sm font-semibold text-ink">รหัสผู้จำหน่าย</span><input name="supplier_code" value="<?php echo e(old('supplier_code', $supplier->supplier_code)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">ชื่อผู้จำหน่าย</span><input name="supplier_name" value="<?php echo e(old('supplier_name', $supplier->supplier_name)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">ผู้ติดต่อ</span><input name="contact_person" value="<?php echo e(old('contact_person', $supplier->contact_person)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">เบอร์โทร</span><input name="phone" value="<?php echo e(old('phone', $supplier->phone)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">LINE ID</span><input name="line_id" value="<?php echo e(old('line_id', $supplier->line_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">อีเมล</span><input name="email" value="<?php echo e(old('email', $supplier->email)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label><span class="text-sm font-semibold text-ink">เลขประจำตัวผู้เสียภาษี</span><input name="tax_id" value="<?php echo e(old('tax_id', $supplier->tax_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                <label class="flex items-center gap-3 pt-7"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $supplier->is_active)): echo 'checked'; endif; ?> class="rounded border-pine-300"><span class="text-sm font-semibold text-ink">เปิดใช้งานผู้จำหน่าย</span></label>
                <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">ที่อยู่</span><textarea name="address" rows="3" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('address', $supplier->address)); ?></textarea></label>
                <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">หมายเหตุ</span><textarea name="notes" rows="3" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('notes', $supplier->notes)); ?></textarea></label>
            </div>

            <button class="mt-6 rounded-md bg-pine-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกผู้จำหน่าย</button>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => ($supplier->exists ? 'แก้ไขผู้จำหน่าย' : 'เพิ่มผู้จำหน่าย').' | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\suppliers\form.blade.php ENDPATH**/ ?>