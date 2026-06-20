<form method="post" action="<?php echo e($action); ?>" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
    <?php echo csrf_field(); ?>
    <?php if($method !== 'POST'): ?>
        <?php echo method_field($method); ?>
    <?php endif; ?>
    <div class="grid gap-5 md:grid-cols-2">
        <label><span class="text-sm font-semibold text-ink">SKU</span><input name="sku" value="<?php echo e(old('sku', $product->sku ?? '')); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label><span class="text-sm font-semibold text-ink">Name</span><input name="name" value="<?php echo e(old('name', $product->name ?? '')); ?>" required class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label><span class="text-sm font-semibold text-ink">Category</span><input name="category" value="<?php echo e(old('category', $product->category ?? '')); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label><span class="text-sm font-semibold text-ink">Unit</span><input name="unit" value="<?php echo e(old('unit', $product->unit ?? 'ชิ้น')); ?>" required class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label><span class="text-sm font-semibold text-ink">Selling Price</span><input name="selling_price" type="number" step="0.01" min="0" value="<?php echo e(old('selling_price', $product->selling_price ?? 0)); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label><span class="text-sm font-semibold text-ink">Cost Price</span><input name="cost_price" type="number" step="0.01" min="0" value="<?php echo e(old('cost_price', $product->cost_price ?? 0)); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">Product Image URL</span><input name="product_image" value="<?php echo e(old('product_image', $product->product_image ?? $product->image ?? '')); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-3 ring-1 ring-pine-200"></label>
        <label class="flex items-center gap-3 rounded-xl bg-pine-50 p-4"><input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $product->is_active ?? true)): echo 'checked'; endif; ?>><span class="text-sm font-semibold text-ink">Active</span></label>
    </div>
    <button class="mt-6 rounded-xl bg-pine-700 px-5 py-3 text-sm font-semibold text-white"><?php echo e($submitLabel); ?></button>
</form>
<?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\products\_form.blade.php ENDPATH**/ ?>