<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">Supplier Management</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">ทะเบียนผู้จำหน่าย</h1>
                <p class="mt-2 text-sm text-pine-700">จัดการข้อมูลร้านค้า โรงไม้ และผู้ขายวัตถุดิบสำหรับงานเฟอร์นิเจอร์</p>
            </div>
            <a href="<?php echo e(route('admin.suppliers.create')); ?>" class="w-fit rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">เพิ่มผู้จำหน่าย</a>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="grid gap-4 md:hidden">
            <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-pine-500"><?php echo e($supplier->supplier_code); ?></p>
                            <a href="<?php echo e(route('admin.suppliers.show', $supplier)); ?>" class="mt-1 block text-lg font-semibold text-ink"><?php echo e($supplier->supplier_name); ?></a>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo e($supplier->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600'); ?>"><?php echo e($supplier->is_active ? 'ใช้งาน' : 'พักใช้งาน'); ?></span>
                    </div>
                    <p class="mt-3 text-sm text-pine-700"><?php echo e($supplier->contact_person ?: '-'); ?> · <?php echo e($supplier->phone ?: '-'); ?></p>
                    <p class="mt-1 text-sm text-pine-700">PO <?php echo e(number_format($supplier->purchase_orders_count)); ?> ใบ</p>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="rounded-lg bg-white p-8 text-center text-sm text-pine-700 ring-1 ring-pine-200">ยังไม่มีผู้จำหน่าย</p>
            <?php endif; ?>
        </div>

        <section class="hidden overflow-hidden rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200 md:block">
            <table class="min-w-full table-fixed divide-y divide-pine-200 text-sm">
                <thead class="bg-pine-100 text-pine-700">
                    <tr>
                        <th class="w-28 px-3 py-2 text-left font-semibold">รหัส</th>
                        <th class="px-3 py-2 text-left font-semibold">ผู้จำหน่าย</th>
                        <th class="px-3 py-2 text-left font-semibold">ผู้ติดต่อ</th>
                        <th class="px-3 py-2 text-left font-semibold">โทรศัพท์</th>
                        <th class="w-24 px-3 py-2 text-right font-semibold">PO</th>
                        <th class="w-28 px-3 py-2 text-center font-semibold">สถานะ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pine-100">
                    <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-3 py-3 font-semibold text-pine-700"><?php echo e($supplier->supplier_code); ?></td>
                            <td class="px-3 py-3"><a href="<?php echo e(route('admin.suppliers.show', $supplier)); ?>" class="font-semibold text-ink hover:text-pine-700"><?php echo e($supplier->supplier_name); ?></a></td>
                            <td class="px-3 py-3 text-pine-700"><?php echo e($supplier->contact_person ?: '-'); ?></td>
                            <td class="px-3 py-3 text-pine-700"><?php echo e($supplier->phone ?: '-'); ?></td>
                            <td class="px-3 py-3 text-right text-pine-700"><?php echo e(number_format($supplier->purchase_orders_count)); ?></td>
                            <td class="px-3 py-3 text-center"><span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo e($supplier->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600'); ?>"><?php echo e($supplier->is_active ? 'ใช้งาน' : 'พักใช้งาน'); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-3 py-8 text-center text-pine-700">ยังไม่มีผู้จำหน่าย</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'ผู้จำหน่าย | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\suppliers\index.blade.php ENDPATH**/ ?>