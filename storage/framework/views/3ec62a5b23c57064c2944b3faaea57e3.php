<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">Portfolio</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">จัดการผลงาน</h1>
                <p class="mt-2 text-sm text-pine-700">เพิ่มรูปผลงาน แยกหมวด และควบคุมรูปที่แสดงในหน้า Gallery</p>
            </div>
            <a href="<?php echo e(route('portfolio.index')); ?>" class="w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดูหน้า Gallery</a>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo e(route('admin.portfolio.store')); ?>" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <?php echo csrf_field(); ?>
            <div class="grid gap-5 lg:grid-cols-[1fr_1fr]">
                <div class="grid gap-4">
                    <label>
                        <span class="text-sm font-semibold text-ink">ชื่อผลงาน</span>
                        <input name="title" value="<?php echo e(old('title')); ?>" placeholder="เช่น ชุดห้องนอนไม้สนบ้านคุณเอ" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                    </label>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label>
                            <span class="text-sm font-semibold text-ink">หมวดผลงาน</span>
                            <select name="category" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php if(old('category') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label>
                            <span class="text-sm font-semibold text-ink">ลำดับแสดงผล</span>
                            <input name="sort_order" type="number" min="0" value="<?php echo e(old('sort_order', 0)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        </label>
                    </div>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="active" value="1" checked class="rounded border-pine-300">
                        <span class="text-sm font-semibold text-ink">เปิดแสดงผลหน้า Gallery</span>
                    </label>
                </div>
                <div>
                    <p class="text-sm font-semibold text-ink">อัปโหลดรูปผลงานหลายรูป</p>
                    <div class="mt-2 rounded-lg border border-dashed border-pine-300 bg-pine-50 p-5">
                        <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-portfolio-input class="block w-full text-sm text-pine-700">
                        <p class="mt-3 text-xs text-pine-600">รองรับ jpg, png, webp ขนาดไม่เกิน 5MB ต่อรูป</p>
                        <div data-portfolio-preview class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3"></div>
                    </div>
                    <button class="mt-5 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">เพิ่มรูปผลงาน</button>
                </div>
            </div>
        </form>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Gallery Images</p>
                    <h2 class="mt-1 text-xl font-semibold text-ink">รูปผลงานทั้งหมด</h2>
                </div>
                <p class="text-sm text-pine-700">ทั้งหมด <?php echo e(number_format($portfolioImages->count())); ?> รูป</p>
            </div>

            <?php if($portfolioImages->isNotEmpty()): ?>
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <?php $__currentLoopData = $portfolioImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="overflow-hidden rounded-lg bg-pine-50 ring-1 ring-pine-200">
                            <img src="<?php echo e(asset('storage/'.$image->image_path)); ?>" alt="<?php echo e($image->title ?: $image->category_name); ?>" class="aspect-[4/3] w-full object-cover">
                            <div class="p-4">
                                <form method="post" action="<?php echo e(route('admin.portfolio.update', $image)); ?>" class="grid gap-3">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('patch'); ?>
                                    <label>
                                        <span class="text-xs font-semibold text-ink">ชื่อผลงาน</span>
                                        <input name="title" value="<?php echo e(old('title', $image->title)); ?>" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                    </label>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <label>
                                            <span class="text-xs font-semibold text-ink">หมวด</span>
                                            <select name="category" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>" <?php if(old('category', $image->category) === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </label>
                                        <label>
                                            <span class="text-xs font-semibold text-ink">ลำดับ</span>
                                            <input name="sort_order" type="number" min="0" value="<?php echo e(old('sort_order', $image->sort_order)); ?>" class="mt-1 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200">
                                        </label>
                                    </div>
                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" name="active" value="1" <?php if($image->active): echo 'checked'; endif; ?> class="rounded border-pine-300">
                                        <span class="text-sm font-semibold text-ink">เปิดแสดงผล</span>
                                    </label>
                                    <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">บันทึกข้อมูลรูป</button>
                                </form>
                                <form method="post" action="<?php echo e(route('admin.portfolio.destroy', $image)); ?>" class="mt-3">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('delete'); ?>
                                    <button class="w-full rounded-md bg-white px-4 py-2 text-sm font-semibold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50">ลบรูปนี้</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="rounded-lg border border-dashed border-pine-300 p-8 text-center text-pine-700">ยังไม่มีรูปผลงาน</div>
            <?php endif; ?>
        </section>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.querySelector('[data-portfolio-input]');
        const preview = document.querySelector('[data-portfolio-preview]');

        input?.addEventListener('change', () => {
            preview.innerHTML = '';
            Array.from(input.files || []).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const image = document.createElement('img');
                    image.src = event.target.result;
                    image.alt = file.name;
                    image.className = 'aspect-square w-full rounded-md object-cover ring-1 ring-pine-200';
                    preview.appendChild(image);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'จัดการผลงาน | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\portfolio\index.blade.php ENDPATH**/ ?>