<?php $__env->startSection('content'); ?>
<section class="bg-[linear-gradient(120deg,#fbf7ef_0%,#ffffff_55%,#f0dfc3_100%)]">
    <div class="mx-auto max-w-6xl px-5 py-14">
        <p class="text-sm font-semibold text-pine-500">Customer Reviews</p>
        <div class="mt-3 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-4xl font-semibold text-ink md:text-5xl">รีวิวจากลูกค้า <?php echo e(company()->display_name); ?></h1>
                <p class="mt-4 max-w-2xl leading-8 text-pine-700">เสียงตอบรับจากลูกค้าที่สั่งทำเฟอร์นิเจอร์ไม้สน ทั้งงานห้องนอน ห้องนั่งเล่น ห้องอาหาร และห้องทำงาน</p>
            </div>
            <a href="<?php echo e(route('lead.create')); ?>" class="inline-flex w-fit items-center justify-center rounded-full bg-pine-700 px-6 py-3 text-sm font-semibold text-white hover:bg-pine-500">ขอประเมินราคา</a>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-6xl px-5 py-12">
        <?php if($reviews->isNotEmpty()): ?>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-pine-200">
                        <?php if($review->image_path): ?>
                            <img src="<?php echo e(asset('storage/'.$review->image_path)); ?>" alt="ผลงานของ <?php echo e($review->customer_name); ?>" class="aspect-[4/3] w-full object-cover">
                        <?php else: ?>
                            <div class="aspect-[4/3] bg-[linear-gradient(135deg,#d8b47a,#fff7ed,#ffffff)] p-6">
                                <div class="h-full rounded-md bg-white/45 p-4">
                                    <div class="h-1/2 rounded bg-pine-200"></div>
                                    <div class="mt-4 h-8 rounded bg-pine-300"></div>
                                    <div class="mt-3 h-8 w-2/3 rounded bg-white/80"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="p-5">
                            <p class="text-lg tracking-wide text-amber-500"><?php echo e(str_repeat('★', $review->rating)); ?><span class="text-pine-200"><?php echo e(str_repeat('★', 5 - $review->rating)); ?></span></p>
                            <p class="mt-4 min-h-24 text-lg leading-8 text-ink">"<?php echo e($review->review_text); ?>"</p>
                            <div class="mt-5 border-t border-pine-100 pt-4">
                                <p class="font-semibold text-ink">คุณ<?php echo e($review->customer_name); ?></p>
                                <?php if($review->province): ?>
                                    <p class="mt-1 text-sm text-pine-700">จังหวัด<?php echo e($review->province); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="rounded-lg border border-dashed border-pine-300 bg-pine-50 p-10 text-center">
                <h2 class="text-xl font-semibold text-ink">ยังไม่มีรีวิวลูกค้า</h2>
                <p class="mt-2 text-pine-700">เมื่อแอดมินเพิ่มรีวิวที่เปิดแสดงผล รีวิวจะแสดงในหน้านี้โดยอัตโนมัติ</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', ['title' => 'รีวิวลูกค้า | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\reviews.blade.php ENDPATH**/ ?>