<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Product Catalog</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">สินค้า</h1>
                </div>
                <a href="<?php echo e(route('admin.products.create')); ?>" class="w-fit rounded-xl bg-pine-700 px-5 py-3 text-sm font-semibold text-white">เพิ่มสินค้า</a>
            </div>
            <?php if(session('success')): ?>
                <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-pine-200">
                <table class="min-w-full divide-y divide-pine-200 text-sm">
                    <thead class="bg-pine-100 text-pine-700">
                        <tr>
                            <th class="px-4 py-3 text-left">SKU</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-right">Selling Price</th>
                            <th class="px-4 py-3 text-right">Cost Price</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pine-100">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-3 font-semibold text-pine-700"><?php echo e($product->sku ?: '-'); ?></td>
                                <td class="px-4 py-3 font-semibold text-ink"><?php echo e($product->name); ?></td>
                                <td class="px-4 py-3 text-pine-700"><?php echo e($product->category ?: '-'); ?></td>
                                <td class="px-4 py-3 text-right text-pine-700">฿<?php echo e(number_format((float) $product->selling_price, 2)); ?></td>
                                <td class="px-4 py-3 text-right text-pine-700">฿<?php echo e(number_format((float) ($product->cost_price ?: $product->total_cost), 2)); ?></td>
                                <td class="px-4 py-3"><span class="rounded-full bg-pine-100 px-3 py-1 text-xs font-semibold text-pine-700"><?php echo e($product->is_active ? 'Active' : 'Inactive'); ?></span></td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?php echo e(route('admin.products.bom', $product)); ?>" class="rounded-lg bg-pine-50 px-3 py-2 font-semibold text-pine-700">BOM</a>
                                        <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="rounded-lg bg-white px-3 py-2 font-semibold text-pine-700 ring-1 ring-pine-200">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="px-4 py-10 text-center text-pine-700">ยังไม่มีสินค้า</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'สินค้า | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\products\index.blade.php ENDPATH**/ ?>