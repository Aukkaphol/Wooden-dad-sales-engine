<?php
    $hero = $sections['hero'] ?? null;
    $workflow = $sections['workflow'] ?? null;
    $trust = $sections['trust'] ?? null;
    $finalCta = $sections['final_cta'] ?? null;
?>

<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-pine-500">การตลาด</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">จัดการหน้าแรก</h1>
                <p class="mt-2 text-sm text-pine-700">แก้ไขข้อความ รูปภาพ หมวดเซ็ตเฟอร์นิเจอร์ และ CTA ของ Landing Page</p>
            </div>
            <a href="<?php echo e(route('home')); ?>" class="w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">ดูหน้าแรก</a>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <?php if($hero): ?>
            <form method="post" action="<?php echo e(route('admin.marketing.homepage.sections.update', $hero)); ?>" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <?php echo csrf_field(); ?>
                <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                    <div>
                        <h2 class="text-xl font-semibold text-ink">Hero Section</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">หัวข้อหลัก</span><input name="title" value="<?php echo e(old('title', $hero->title)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                            <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">ข้อความรอง</span><textarea name="subtitle" rows="2" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('subtitle', $hero->subtitle)); ?></textarea></label>
                            <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">รายละเอียด</span><textarea name="description" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('description', $hero->description)); ?></textarea></label>
                            <label><span class="text-sm font-semibold text-ink">ปุ่มหลัก</span><input name="button_text" value="<?php echo e(old('button_text', $hero->button_text)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                            <label><span class="text-sm font-semibold text-ink">ลิงก์ปุ่ม</span><input name="button_url" value="<?php echo e(old('button_url', $hero->button_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                            <label class="flex items-center gap-3"><input type="checkbox" name="active" value="1" <?php if($hero->active): echo 'checked'; endif; ?> class="rounded border-pine-300"><span class="text-sm font-semibold text-ink">เปิดแสดงผล</span></label>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-ink">รูป Hero</p>
                        <div class="mt-2 aspect-[4/3] overflow-hidden rounded-lg bg-[linear-gradient(135deg,#d8b47a,#fff7ed,#b7793b)] ring-1 ring-pine-200">
                            <?php if($hero->image_path): ?>
                                <img data-image-preview="hero" src="<?php echo e(asset('storage/'.$hero->image_path)); ?>" class="h-full w-full object-cover" alt="Hero">
                            <?php else: ?>
                                <div data-image-preview="hero" class="flex h-full w-full items-center justify-center text-sm font-semibold text-white/90">ยังไม่มีรูป</div>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-input="hero" class="mt-3 block w-full text-sm text-pine-700">
                        <button class="mt-5 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึก Hero</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <section class="mt-8 rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
            <div>
                <p class="text-sm font-semibold text-pine-500">Furniture Set Categories</p>
                <h2 class="mt-1 text-xl font-semibold text-ink">จัดการหมวดเซ็ตเฟอร์นิเจอร์</h2>
            </div>
            <div class="mt-6 grid gap-5 xl:grid-cols-2">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <form method="post" action="<?php echo e(route('admin.marketing.homepage.categories.update', $category)); ?>" enctype="multipart/form-data" class="rounded-lg bg-pine-50 p-5 ring-1 ring-pine-200">
                        <?php echo csrf_field(); ?>
                        <div class="grid gap-5 lg:grid-cols-[1fr_220px]">
                            <div class="grid gap-3">
                                <label><span class="text-sm font-semibold text-ink">ชื่อหมวด</span><input name="name" value="<?php echo e(old('name', $category->name)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200"></label>
                                <label><span class="text-sm font-semibold text-ink">รายละเอียดสั้น</span><textarea name="short_description" rows="2" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200"><?php echo e(old('short_description', $category->short_description)); ?></textarea></label>
                                <label><span class="text-sm font-semibold text-ink">รายละเอียดเต็ม</span><textarea name="full_description" rows="3" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200"><?php echo e(old('full_description', $category->full_description)); ?></textarea></label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label><span class="text-sm font-semibold text-ink">ราคาเริ่มต้น</span><input name="start_price" type="number" step="0.01" min="0" value="<?php echo e(old('start_price', $category->start_price)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200"></label>
                                    <label><span class="text-sm font-semibold text-ink">ลำดับแสดงผล</span><input name="sort_order" type="number" min="0" value="<?php echo e(old('sort_order', $category->sort_order)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2 text-sm ring-1 ring-pine-200"></label>
                                </div>
                                <label class="flex items-center gap-3"><input type="checkbox" name="active" value="1" <?php if($category->active): echo 'checked'; endif; ?> class="rounded border-pine-300"><span class="text-sm font-semibold text-ink">เปิดแสดงผล</span></label>
                            </div>
                            <div>
                                <div class="aspect-[4/3] overflow-hidden rounded-lg bg-[linear-gradient(135deg,#d8b47a,#f7ead7,#ffffff)] ring-1 ring-pine-200">
                                    <?php if($category->image_path): ?>
                                        <img data-image-preview="category-<?php echo e($category->id); ?>" src="<?php echo e(asset('storage/'.$category->image_path)); ?>" class="h-full w-full object-cover" alt="<?php echo e($category->name); ?>">
                                    <?php else: ?>
                                        <div data-image-preview="category-<?php echo e($category->id); ?>" class="flex h-full w-full items-center justify-center text-sm font-semibold text-pine-700">ยังไม่มีรูป</div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-input="category-<?php echo e($category->id); ?>" class="mt-3 block w-full text-sm text-pine-700">
                                <button class="mt-4 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึกหมวดนี้</button>
                            </div>
                        </div>
                    </form>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            <?php $__currentLoopData = [['label' => 'Workflow Section', 'section' => $workflow], ['label' => 'Trust Section', 'section' => $trust], ['label' => 'CTA ท้ายหน้า', 'section' => $finalCta]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($block['section']): ?>
                    <?php
                        $section = $block['section'];
                    ?>
                    <form method="post" action="<?php echo e(route('admin.marketing.homepage.sections.update', $section)); ?>" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <?php echo csrf_field(); ?>
                        <h2 class="text-lg font-semibold text-ink"><?php echo e($block['label']); ?></h2>
                        <div class="mt-5 grid gap-3">
                            <label><span class="text-sm font-semibold text-ink">หัวข้อ</span><input name="title" value="<?php echo e(old('title', $section->title)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2 text-sm ring-1 ring-pine-200"></label>
                            <label><span class="text-sm font-semibold text-ink">ข้อความรอง</span><textarea name="subtitle" rows="2" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2 text-sm ring-1 ring-pine-200"><?php echo e(old('subtitle', $section->subtitle)); ?></textarea></label>
                            <label><span class="text-sm font-semibold text-ink">รายละเอียด</span><textarea name="description" rows="4" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2 text-sm ring-1 ring-pine-200"><?php echo e(old('description', $section->description)); ?></textarea></label>
                            <?php if($section->section_key === 'final_cta'): ?>
                                <label><span class="text-sm font-semibold text-ink">ข้อความปุ่ม</span><input name="button_text" value="<?php echo e(old('button_text', $section->button_text)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2 text-sm ring-1 ring-pine-200"></label>
                                <label><span class="text-sm font-semibold text-ink">ลิงก์ปุ่ม</span><input name="button_url" value="<?php echo e(old('button_url', $section->button_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2 text-sm ring-1 ring-pine-200"></label>
                            <?php endif; ?>
                            <label class="flex items-center gap-3"><input type="checkbox" name="active" value="1" <?php if($section->active): echo 'checked'; endif; ?> class="rounded border-pine-300"><span class="text-sm font-semibold text-ink">เปิดแสดงผล</span></label>
                        </div>
                        <button class="mt-5 w-full rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">บันทึก</button>
                    </form>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll('[data-image-input]').forEach((input) => {
        input.addEventListener('change', () => {
            const key = input.dataset.imageInput;
            const target = document.querySelector(`[data-image-preview="${key}"]`);
            const file = input.files?.[0];
            if (!target || !file) return;
            const url = URL.createObjectURL(file);
            if (target.tagName === 'IMG') {
                target.src = url;
                return;
            }
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Preview';
            img.className = 'h-full w-full object-cover';
            img.dataset.imagePreview = key;
            target.replaceWith(img);
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'จัดการหน้าแรก | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\marketing\homepage.blade.php ENDPATH**/ ?>