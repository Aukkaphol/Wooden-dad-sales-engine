<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">คิวงานผลิต</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">คิวงานผลิต Wooden Dad Design</h1>
                    <p class="mt-2 text-sm text-pine-700">สร้างจากใบเสนอราคาที่อนุมัติแล้ว และผูกกับข้อมูลลูกค้า/ใบเสนอราคาเดิมทั้งหมด</p>
                </div>
                <a href="<?php echo e(route('admin.leads.index')); ?>" class="inline-flex w-fit rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-100">กลับ CRM</a>
            </div>

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-pine-200">
                        <dt class="text-sm font-medium text-pine-700"><?php echo e($label); ?></dt>
                        <dd class="mt-2 text-2xl font-semibold text-ink"><?php echo e(number_format($counts[$status] ?? 0)); ?></dd>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </dl>

            <section class="mt-8 rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200" data-production-board>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">บอร์ดคิวงานผลิต</h2>
                        <p class="mt-1 text-sm text-pine-700">ลากการ์ดเพื่อเปลี่ยนขั้นตอนงานผลิต</p>
                    </div>
                    <p class="hidden rounded-md bg-pine-100 px-3 py-2 text-sm font-semibold text-pine-700" data-production-message></p>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-7">
                    <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg bg-pine-50 p-3 ring-1 ring-pine-200" data-production-column data-status="<?php echo e($status); ?>">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold text-ink"><?php echo e($label); ?></h3>
                                <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-pine-700 ring-1 ring-pine-200" data-stage-count><?php echo e(($ordersByStage[$status] ?? collect())->count()); ?></span>
                            </div>
                            <div class="mt-4 min-h-36 space-y-3 rounded-md border border-dashed border-transparent p-1 transition" data-production-dropzone>
                                <?php $__empty_1 = true; $__currentLoopData = ($ordersByStage[$status] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <article
                                        class="cursor-grab rounded-md bg-white p-3 shadow-sm ring-1 ring-pine-200 transition active:cursor-grabbing"
                                        draggable="true"
                                        data-production-card
                                        data-order-id="<?php echo e($order->id); ?>"
                                        data-update-url="<?php echo e(route('admin.production.status', $order)); ?>"
                                    >
                                        <a href="<?php echo e(route('admin.production.show', $order)); ?>" class="block text-sm font-semibold text-ink underline-offset-4 hover:text-pine-700 hover:underline"><?php echo e($order->production_order_number); ?></a>
                                        <p class="mt-1 truncate text-xs text-pine-700"><?php echo e($order->lead->name); ?> · <?php echo e($order->quotation->quotation_number); ?></p>
                                        <p class="mt-2 text-xs font-semibold text-pine-700">฿<?php echo e(number_format((float) $order->quotation->subtotal, 2)); ?></p>
                                        <div class="mt-3 flex flex-wrap gap-1">
                                            <?php $__empty_2 = true; $__currentLoopData = $order->craftsmen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $craftsman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <span class="rounded-full bg-pine-100 px-2 py-1 text-xs font-semibold text-pine-700"><?php echo e($craftsman->name); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <span class="rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">ยังไม่ระบุช่าง</span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo e(route('admin.production.show', $order)); ?>" class="mt-3 inline-flex rounded-md bg-pine-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pine-500">เปิดงาน</a>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="rounded-md border border-dashed border-pine-300 p-4 text-center text-sm text-pine-700">ไม่มีงาน</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        </div>
    </section>

    <script>
        (() => {
            const board = document.querySelector('[data-production-board]');
            if (!board) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const message = board.querySelector('[data-production-message]');
            let draggedCard = null;

            const setMessage = (text, isError = false) => {
                if (!message) return;
                message.textContent = text;
                message.classList.remove('hidden', 'bg-pine-100', 'text-pine-700', 'bg-rose-50', 'text-rose-700');
                message.classList.add(isError ? 'bg-rose-50' : 'bg-pine-100', isError ? 'text-rose-700' : 'text-pine-700');
            };

            const refreshCounts = () => {
                board.querySelectorAll('[data-production-column]').forEach((column) => {
                    const count = column.querySelectorAll('[data-production-card]').length;
                    const counter = column.querySelector('[data-stage-count]');
                    if (counter) counter.textContent = count;
                });
            };

            const moveOrder = async (card, column) => {
                const status = column.dataset.status;
                const previousDropzone = card.closest('[data-production-dropzone]');
                const dropzone = column.querySelector('[data-production-dropzone]');

                if (!status || !dropzone || !card.dataset.updateUrl || !token) {
                    setMessage('ไม่สามารถย้ายงานได้', true);
                    return;
                }

                dropzone.appendChild(card);
                refreshCounts();

                try {
                    const response = await fetch(card.dataset.updateUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ status }),
                    });

                    if (!response.ok) throw new Error('อัปเดตไม่สำเร็จ');
                    setMessage('อัปเดตขั้นตอนผลิตแล้ว');
                } catch (error) {
                    if (previousDropzone) previousDropzone.appendChild(card);
                    refreshCounts();
                    setMessage('อัปเดตไม่สำเร็จ กรุณาลองใหม่', true);
                }
            };

            board.querySelectorAll('[data-production-card]').forEach((card) => {
                card.addEventListener('dragstart', (event) => {
                    draggedCard = card;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', card.dataset.orderId);
                    card.classList.add('opacity-60');
                });
                card.addEventListener('dragend', () => {
                    card.classList.remove('opacity-60');
                    draggedCard = null;
                    board.querySelectorAll('[data-production-dropzone]').forEach((zone) => zone.classList.remove('border-pine-400', 'bg-white'));
                });
            });

            board.querySelectorAll('[data-production-column]').forEach((column) => {
                const dropzone = column.querySelector('[data-production-dropzone]');
                if (!dropzone) return;

                dropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                    dropzone.classList.add('border-pine-400', 'bg-white');
                });
                dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-pine-400', 'bg-white'));
                dropzone.addEventListener('drop', async (event) => {
                    event.preventDefault();
                    dropzone.classList.remove('border-pine-400', 'bg-white');
                    if (draggedCard) await moveOrder(draggedCard, column);
                });
            });
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'คิวงานผลิต | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\production\index.blade.php ENDPATH**/ ?>