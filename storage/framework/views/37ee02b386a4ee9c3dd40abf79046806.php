<?php
    $hero = $sections['hero'] ?? null;
    $workflow = $sections['workflow'] ?? null;
    $trust = $sections['trust'] ?? null;
    $finalCta = $sections['final_cta'] ?? null;
    $steps = ['เลือกเซ็ต', 'ส่งขนาดพื้นที่', 'ประเมินราคา', 'ออกใบเสนอราคา', 'ผลิต', 'ส่งมอบ/ติดตั้ง'];
?>

<?php $__env->startSection('content'); ?>
    <?php if(! $hero || $hero->active): ?>
        <section class="bg-[linear-gradient(120deg,#fbf7ef_0%,#ffffff_52%,#f0dfc3_100%)]">
            <div class="mx-auto grid max-w-6xl items-center gap-10 px-5 py-16 md:grid-cols-[1.05fr_.95fr] md:py-20">
                <div>
                    <p class="mb-4 text-sm font-semibold text-pine-500"><?php echo e($hero?->subtitle ?? 'เฟอร์นิเจอร์ไม้สนสั่งทำ'); ?></p>
                    <h1 class="max-w-2xl text-4xl font-semibold leading-tight text-ink md:text-6xl"><?php echo e($hero?->title ?? 'Wooden Dad Design'); ?></h1>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-pine-700"><?php echo e($hero?->description ?? 'เลือกเซ็ตเฟอร์นิเจอร์ที่เข้ากับพื้นที่จริงของบ้านคุณ พร้อมประเมินราคาและออกแบบเบื้องต้นฟรี'); ?></p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="<?php echo e($hero?->button_url ?: route('lead.create')); ?>" class="inline-flex items-center justify-center rounded-full bg-pine-500 px-6 py-3 font-semibold text-white shadow-sm hover:bg-pine-700"><?php echo e($hero?->button_text ?: 'ขอประเมินราคา'); ?></a>
                        <a href="#furniture-sets" class="inline-flex items-center justify-center rounded-full border border-pine-300 bg-white px-6 py-3 font-semibold text-pine-700 hover:border-pine-500">ดูหมวดเซ็ตเฟอร์นิเจอร์</a>
                    </div>
                </div>
                <div class="overflow-hidden rounded-lg border border-pine-200 bg-white shadow-sm">
                    <div class="aspect-[4/3] bg-[linear-gradient(135deg,#d8b47a_0%,#f6ead8_42%,#ffffff_43%,#ffffff_64%,#c99b5f_100%)]">
                        <?php if($hero?->image_path): ?>
                            <img src="<?php echo e(asset('storage/'.$hero->image_path)); ?>" alt="<?php echo e($hero?->title ?? 'Wooden Dad Design'); ?>" class="h-full w-full object-cover">
                        <?php else: ?>
                            <div class="h-full p-6">
                                <div class="h-full rounded-md border border-white/70 bg-white/35 p-5">
                                    <div class="h-24 w-3/5 rounded-sm bg-pine-200"></div>
                                    <div class="mt-8 grid grid-cols-[1.3fr_.7fr] gap-4">
                                        <div class="h-36 rounded-sm bg-pine-300/80"></div>
                                        <div class="space-y-3">
                                            <div class="h-16 rounded-sm bg-white/75"></div>
                                            <div class="h-16 rounded-sm bg-pine-100"></div>
                                        </div>
                                    </div>
                                    <div class="mt-4 h-10 rounded-sm bg-pine-400/55"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section id="furniture-sets" class="bg-white">
        <div class="mx-auto max-w-6xl px-5 py-14">
            <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Furniture Set Collection</p>
                    <h2 class="mt-2 text-3xl font-semibold text-ink">หมวดเซ็ตเฟอร์นิเจอร์</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-pine-700">เลือกหมวดที่เหมาะกับพื้นที่ของคุณ แล้วส่งข้อมูลให้ทีมช่วยประเมินราคาและแนวทางผลิต</p>
                </div>
            </div>
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="overflow-hidden rounded-lg border border-pine-200 bg-white shadow-sm">
                        <div class="aspect-[4/3] bg-[linear-gradient(135deg,#d8b47a,#fff7ed,#ffffff)]">
                            <?php if($category->image_path): ?>
                                <img src="<?php echo e(asset('storage/'.$category->image_path)); ?>" alt="<?php echo e($category->name); ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                                <div class="flex h-full items-center justify-center p-6">
                                    <div class="h-full w-full rounded-md bg-white/45 p-4">
                                        <div class="h-1/2 rounded bg-pine-200"></div>
                                        <div class="mt-4 h-8 rounded bg-pine-300"></div>
                                        <div class="mt-3 h-8 w-2/3 rounded bg-white/80"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-ink"><?php echo e($category->name); ?></h3>
                            <p class="mt-3 min-h-20 leading-7 text-pine-700"><?php echo e($category->short_description); ?></p>
                            <?php if($category->start_price): ?>
                                <p class="mt-4 text-sm font-semibold text-pine-700">เริ่มต้น ฿<?php echo e(number_format((float) $category->start_price, 2)); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo e(route('lead.create')); ?>" class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pine-500">ขอประเมินราคา</a>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="rounded-lg border border-dashed border-pine-300 p-8 text-center text-pine-700 md:col-span-2 xl:col-span-4">ยังไม่มีหมวดเซ็ตเฟอร์นิเจอร์ที่เปิดแสดงผล</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if(($latestPortfolioImages ?? collect())->isNotEmpty()): ?>
        <section class="bg-pine-50">
            <div class="mx-auto max-w-6xl px-5 py-14">
                <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-pine-500">Portfolio</p>
                        <h2 class="mt-2 text-3xl font-semibold text-ink">ผลงานล่าสุด</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-pine-700">ตัวอย่างงานเฟอร์นิเจอร์ไม้สนสั่งทำจากพื้นที่จริงของลูกค้า</p>
                    </div>
                    <a href="<?php echo e(route('portfolio.index')); ?>" class="inline-flex w-fit items-center justify-center rounded-full border border-pine-300 bg-white px-5 py-2.5 text-sm font-semibold text-pine-700 hover:border-pine-500">ดูผลงานทั้งหมด</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <?php $__currentLoopData = $latestPortfolioImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolioImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="group overflow-hidden rounded-lg bg-white text-left shadow-sm ring-1 ring-pine-200" data-home-gallery-open="<?php echo e(asset('storage/'.$portfolioImage->image_path)); ?>" data-home-gallery-title="<?php echo e($portfolioImage->title ?: $portfolioImage->category_name); ?>">
                            <img src="<?php echo e(asset('storage/'.$portfolioImage->image_path)); ?>" alt="<?php echo e($portfolioImage->title ?: $portfolioImage->category_name); ?>" class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                            <span class="block p-4">
                                <span class="block text-xs font-semibold text-pine-500"><?php echo e($portfolioImage->category_name); ?></span>
                                <span class="mt-1 block font-semibold text-ink"><?php echo e($portfolioImage->title ?: 'ผลงาน '.$portfolioImage->category_name); ?></span>
                            </span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <dialog id="homeGalleryDialog" class="w-[min(920px,92vw)] rounded-lg bg-transparent p-0 backdrop:bg-black/75">
            <div class="overflow-hidden rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between gap-4 border-b border-pine-100 px-4 py-3">
                    <p id="homeGalleryDialogTitle" class="font-semibold text-ink">ผลงาน</p>
                    <button type="button" class="rounded-full bg-pine-50 px-3 py-1.5 text-sm font-semibold text-pine-700 hover:bg-pine-100" data-home-gallery-close>ปิด</button>
                </div>
                <img id="homeGalleryDialogImage" src="" alt="" class="max-h-[78vh] w-full bg-pine-50 object-contain">
            </div>
        </dialog>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dialog = document.getElementById('homeGalleryDialog');
                const image = document.getElementById('homeGalleryDialogImage');
                const title = document.getElementById('homeGalleryDialogTitle');

                document.querySelectorAll('[data-home-gallery-open]').forEach((button) => {
                    button.addEventListener('click', () => {
                        image.src = button.dataset.homeGalleryOpen;
                        image.alt = button.dataset.homeGalleryTitle || 'ผลงาน Wooden Dad Design';
                        title.textContent = button.dataset.homeGalleryTitle || 'ผลงาน';
                        dialog.showModal();
                    });
                });

                document.querySelector('[data-home-gallery-close]')?.addEventListener('click', () => dialog.close());
                dialog?.addEventListener('click', (event) => {
                    if (event.target === dialog) {
                        dialog.close();
                    }
                });
            });
        </script>
    <?php endif; ?>

    <?php if(($customerReviews ?? collect())->isNotEmpty()): ?>
        <section class="bg-white">
            <div class="mx-auto max-w-6xl px-5 py-14">
                <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-pine-500">Customer Reviews</p>
                        <h2 class="mt-2 text-3xl font-semibold text-ink">รีวิวลูกค้า</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-pine-700">เสียงตอบรับจากลูกค้าที่เลือกงานเฟอร์นิเจอร์ไม้สนสั่งทำกับ Wooden Dad Design</p>
                    </div>
                    <a href="<?php echo e(route('reviews.index')); ?>" class="inline-flex w-fit items-center justify-center rounded-full border border-pine-300 bg-white px-5 py-2.5 text-sm font-semibold text-pine-700 hover:border-pine-500">อ่านรีวิวทั้งหมด</a>
                </div>

                <div class="overflow-hidden" data-review-carousel>
                    <div class="flex transition-transform duration-700 ease-out" data-review-track>
                        <?php $__currentLoopData = $customerReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="min-w-full px-1 md:min-w-[50%] lg:min-w-[33.333%]">
                                <div class="h-full overflow-hidden rounded-lg bg-pine-50 shadow-sm ring-1 ring-pine-200">
                                    <?php if($review->image_path): ?>
                                        <img src="<?php echo e(asset('storage/'.$review->image_path)); ?>" alt="ผลงานของ <?php echo e($review->customer_name); ?>" class="aspect-[4/3] w-full object-cover">
                                    <?php endif; ?>
                                    <div class="p-6">
                                        <p class="text-lg tracking-wide text-amber-500"><?php echo e(str_repeat('★', $review->rating)); ?><span class="text-pine-200"><?php echo e(str_repeat('★', 5 - $review->rating)); ?></span></p>
                                        <p class="mt-4 min-h-24 text-lg leading-8 text-ink">"<?php echo e($review->review_text); ?>"</p>
                                        <div class="mt-5 border-t border-pine-200 pt-4">
                                            <p class="font-semibold text-ink">คุณ<?php echo e($review->customer_name); ?></p>
                                            <?php if($review->province): ?>
                                                <p class="mt-1 text-sm text-pine-700">จังหวัด<?php echo e($review->province); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const carousel = document.querySelector('[data-review-carousel]');
                const track = document.querySelector('[data-review-track]');

                if (!carousel || !track) {
                    return;
                }

                const slides = Array.from(track.children);
                let index = 0;

                const visibleSlides = () => {
                    if (window.innerWidth >= 1024) {
                        return 3;
                    }

                    if (window.innerWidth >= 768) {
                        return 2;
                    }

                    return 1;
                };

                const move = () => {
                    const maxIndex = Math.max(0, slides.length - visibleSlides());
                    index = index >= maxIndex ? 0 : index + 1;
                    track.style.transform = `translateX(-${index * (100 / visibleSlides())}%)`;
                };

                if (slides.length > 1) {
                    window.setInterval(move, 3500);
                    window.addEventListener('resize', () => {
                        index = 0;
                        track.style.transform = 'translateX(0)';
                    });
                }
            });
        </script>
    <?php endif; ?>

    <?php if($workflow?->active): ?>
        <section class="bg-pine-50">
            <div class="mx-auto max-w-6xl px-5 py-14">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold text-pine-500"><?php echo e($workflow->subtitle); ?></p>
                    <h2 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($workflow->title); ?></h2>
                    <p class="mt-3 leading-7 text-pine-700"><?php echo e($workflow->description); ?></p>
                </div>
                <div class="mt-8 grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg bg-white p-5 text-center shadow-sm ring-1 ring-pine-200">
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-pine-100 text-sm font-semibold text-pine-700"><?php echo e($index + 1); ?></div>
                            <p class="mt-3 font-semibold text-ink"><?php echo e($step); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if($trust?->active): ?>
        <section class="bg-white">
            <div class="mx-auto grid max-w-6xl gap-8 px-5 py-14 md:grid-cols-[.8fr_1.2fr] md:items-center">
                <div class="aspect-[4/3] rounded-lg bg-[linear-gradient(135deg,#c99b5f,#f6ead8,#ffffff)] p-6 shadow-sm ring-1 ring-pine-200">
                    <div class="h-full rounded-md bg-white/50 p-5">
                        <div class="h-14 rounded bg-pine-200"></div>
                        <div class="mt-5 grid grid-cols-2 gap-4">
                            <div class="h-32 rounded bg-pine-300"></div>
                            <div class="h-32 rounded bg-white/80"></div>
                        </div>
                        <div class="mt-5 h-10 rounded bg-pine-400/60"></div>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-pine-500"><?php echo e($trust->subtitle); ?></p>
                    <h2 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($trust->title); ?></h2>
                    <p class="mt-4 leading-8 text-pine-700"><?php echo e($trust->description); ?></p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-md bg-pine-50 p-4"><p class="font-semibold text-ink">วัดพื้นที่จริง</p></div>
                        <div class="rounded-md bg-pine-50 p-4"><p class="font-semibold text-ink">เสนอราคาชัดเจน</p></div>
                        <div class="rounded-md bg-pine-50 p-4"><p class="font-semibold text-ink">ผลิตตามงานสั่งทำ</p></div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if($finalCta?->active): ?>
        <section class="bg-pine-700">
            <div class="mx-auto max-w-5xl px-5 py-14 text-center">
                <p class="text-sm font-semibold text-pine-100"><?php echo e($finalCta->subtitle); ?></p>
                <h2 class="mt-2 text-3xl font-semibold text-white"><?php echo e($finalCta->title); ?></h2>
                <p class="mx-auto mt-4 max-w-2xl leading-8 text-pine-100"><?php echo e($finalCta->description); ?></p>
                <a href="<?php echo e($finalCta->button_url ?: route('lead.create')); ?>" class="mt-8 inline-flex rounded-full bg-white px-6 py-3 font-semibold text-pine-700 hover:bg-pine-50"><?php echo e($finalCta->button_text ?: 'ขอประเมินราคา'); ?></a>
            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Wooden Dad Design | เฟอร์นิเจอร์ไม้สนสั่งทำ'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views/home.blade.php ENDPATH**/ ?>