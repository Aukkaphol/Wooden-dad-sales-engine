<?php
    $statusClasses = [
        'new_lead' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'contacted' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'site_survey' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20',
        'designing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'quotation_sent' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'negotiation' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'won' => 'bg-green-50 text-green-700 ring-green-600/20',
        'lost' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];
    $maxMonthly = max(1, $monthlyLeads->max('total') ?? 1);
?>

<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">Sales CRM</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">CRM Pipeline ลูกค้า</h1>
                    <p class="mt-2 text-sm text-pine-700">ติดตามลีดจากทุกช่องทาง ตั้งแต่ลูกค้าใหม่จนถึงปิดการขาย</p>
                </div>
                <a href="<?php echo e(route('admin.leads.export', request()->query())); ?>" class="inline-flex w-fit items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-pine-700 shadow-sm ring-1 ring-pine-200 hover:bg-pine-100">ส่งออก Excel</a>
            </div>

            <?php if(session('success')): ?>
                <div class="mt-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Total Leads</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($kpis['total_leads'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Estimated Revenue</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700">฿<?php echo e(number_format($kpis['estimated_revenue'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Deposit Revenue</dt>
                    <dd class="mt-2 text-3xl font-semibold text-emerald-700">฿<?php echo e(number_format($kpis['deposit_revenue'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Conversion Rate</dt>
                    <dd class="mt-2 text-3xl font-semibold text-green-700"><?php echo e(number_format($kpis['conversion_rate'], 1)); ?>%</dd>
                </div>
            </dl>

            <dl class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Leads Today</dt>
                    <dd class="mt-2 text-3xl font-semibold text-blue-700"><?php echo e(number_format($widgets['leads_today'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Leads This Month</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700"><?php echo e(number_format($widgets['leads_this_month'])); ?></dd>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">Pending Follow-up</dt>
                    <dd class="mt-2 text-3xl font-semibold text-rose-700"><?php echo e(number_format($widgets['pending_follow_up'])); ?></dd>
                </div>
            </dl>

            <section class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <h2 class="text-lg font-semibold text-ink">Lead Growth</h2>
                <div class="mt-6 flex h-48 items-end gap-3 overflow-x-auto pb-2">
                    <?php $__empty_1 = true; $__currentLoopData = $monthlyLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex min-w-14 flex-1 flex-col items-center gap-2">
                            <div class="flex h-32 w-full items-end rounded-xl bg-pine-50 px-2">
                                <div class="w-full rounded-t-xl bg-pine-600" style="height: <?php echo e(max(6, ($month->total / $maxMonthly) * 100)); ?>%"></div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-ink"><?php echo e(number_format($month->total)); ?></p>
                                <p class="text-[11px] text-pine-600"><?php echo e($month->month); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="flex h-full w-full items-center justify-center rounded-xl border border-dashed border-pine-300 text-pine-700">ยังไม่มีข้อมูลลีด</div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="mt-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200" data-kanban-board>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">Sales Pipeline</h2>
                        <p class="mt-1 text-sm text-pine-700">ลากการ์ดเพื่อเปลี่ยนสถานะงานขาย</p>
                    </div>
                    <p class="hidden rounded-xl bg-pine-100 px-3 py-2 text-sm font-semibold text-pine-700" data-kanban-message></p>
                </div>
                <div class="mt-5 grid gap-4 xl:grid-cols-4">
                    <?php $__currentLoopData = $kanbanStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl bg-pine-50 p-3 ring-1 ring-pine-200" data-kanban-column data-stage="<?php echo e($stage); ?>">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="font-semibold text-ink"><?php echo e($label); ?></h3>
                                <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-pine-700 ring-1 ring-pine-200" data-stage-count><?php echo e(($kanbanLeads[$stage] ?? collect())->count()); ?></span>
                            </div>
                            <div class="mt-4 min-h-32 space-y-3 rounded-xl border border-dashed border-transparent p-1 transition" data-kanban-dropzone>
                                <?php $__empty_1 = true; $__currentLoopData = ($kanbanLeads[$stage] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <article class="cursor-grab rounded-xl bg-white p-3 shadow-sm ring-1 ring-pine-200 transition active:cursor-grabbing" draggable="true" data-lead-card data-lead-id="<?php echo e($lead->id); ?>" data-update-url="<?php echo e(route('admin.leads.status', $lead)); ?>">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <a href="<?php echo e(route('admin.leads.show', ['lead' => $lead] + request()->query())); ?>" class="block truncate text-sm font-semibold text-ink hover:text-pine-700"><?php echo e($lead->name); ?></a>
                                                <p class="mt-1 text-xs text-pine-700"><?php echo e($lead->phone ?: '-'); ?></p>
                                            </div>
                                            <span class="shrink-0 rounded-full bg-pine-100 px-2 py-1 text-[11px] font-semibold text-pine-700"><?php echo e($lead->source_label); ?></span>
                                        </div>
                                        <p class="mt-3 truncate text-xs text-pine-700"><?php echo e($lead->province ?: '-'); ?> · <?php echo e($lead->budget ?: '-'); ?></p>
                                        <form action="<?php echo e(route('admin.leads.status', $lead)); ?>" method="post" class="mt-3 flex gap-2">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <select name="lead_status" data-card-status-select class="min-w-0 flex-1 rounded-lg border-0 bg-pine-50 px-2 py-1.5 text-xs ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                                <?php $__currentLoopData = $kanbanStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $stageLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($value); ?>" <?php if(($lead->status ?: $lead->lead_status) === $value): echo 'selected'; endif; ?>><?php echo e($stageLabel); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <button class="rounded-lg bg-pine-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pine-500">ย้าย</button>
                                        </form>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="rounded-xl border border-dashed border-pine-300 p-4 text-center text-sm text-pine-700">ไม่มีลีด</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <form method="get" action="<?php echo e(route('admin.leads.index')); ?>" class="mt-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-7">
                    <label class="xl:col-span-2">
                        <span class="text-sm font-semibold text-ink">ค้นหาชื่อหรือเบอร์โทร</span>
                        <input name="search" value="<?php echo e($filters['search'] ?? ''); ?>" placeholder="เช่น สมชาย หรือ 081..." class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 placeholder:text-pine-400 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">จังหวัด</span>
                        <select name="province" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกจังหวัด</option>
                            <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($province); ?>" <?php if(($filters['province'] ?? '') === $province): echo 'selected'; endif; ?>><?php echo e($province); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">สถานะ CRM</span>
                        <select name="lead_status" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกสถานะ</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(($filters['lead_status'] ?? '') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">Source</span>
                        <select name="source" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกช่องทาง</option>
                            <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(($filters['source'] ?? '') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ตั้งแต่วันที่</span>
                        <input name="date_from" type="date" value="<?php echo e($filters['date_from'] ?? ''); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ถึงวันที่</span>
                        <input name="date_to" type="date" value="<?php echo e($filters['date_to'] ?? ''); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-xl bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">ค้นหา</button>
                    <a href="<?php echo e(route('admin.leads.index')); ?>" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-pine-700 ring-1 ring-inset ring-pine-200 hover:bg-pine-100">ล้างตัวกรอง</a>
                </div>
            </form>

            <div class="mt-8 hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-pine-200 lg:block">
                <table class="min-w-full table-fixed divide-y divide-pine-200">
                    <thead class="bg-pine-100">
                        <tr>
                            <th class="w-56 px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">Customer</th>
                            <th class="w-28 px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">Source</th>
                            <th class="w-32 px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">Province</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">Budget</th>
                            <th class="w-44 px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">Status</th>
                            <th class="w-36 px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">Created</th>
                            <th class="w-28 px-4 py-3 text-right text-xs font-semibold uppercase text-pine-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pine-100 bg-white">
                        <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-pine-50">
                                <td class="px-4 py-4">
                                    <div class="truncate font-semibold text-ink"><?php echo e($lead->name); ?></div>
                                    <div class="mt-1 truncate text-sm text-pine-700"><?php echo e($lead->phone ?: '-'); ?></div>
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-pine-700"><?php echo e($lead->source_label); ?></td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->province ?: '-'); ?></td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->budget ?: '-'); ?></td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?php echo e($statusClasses[$lead->status ?: $lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20'); ?>"><?php echo e($lead->lead_status_label); ?></span>
                                </td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->created_at->format('d/m/Y H:i')); ?></td>
                                <td class="px-4 py-4 text-right">
                                    <a href="<?php echo e(route('admin.leads.show', ['lead' => $lead] + request()->query())); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">เปิดดู</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-pine-700">ไม่พบข้อมูลลูกค้าตามเงื่อนไขที่เลือก</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 space-y-4 lg:hidden">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h2 class="truncate font-semibold text-ink"><?php echo e($lead->name); ?></h2>
                                <p class="mt-1 text-sm text-pine-700"><?php echo e($lead->phone ?: '-'); ?> · <?php echo e($lead->province ?: '-'); ?></p>
                                <p class="mt-1 text-xs font-semibold text-pine-700"><?php echo e($lead->source_label); ?></p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?php echo e($statusClasses[$lead->status ?: $lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20'); ?>"><?php echo e($lead->lead_status_label); ?></span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-pine-500">งบประมาณ</p>
                                <p class="font-medium text-ink"><?php echo e($lead->budget ?: '-'); ?></p>
                            </div>
                            <div>
                                <p class="text-pine-500">วันสร้าง</p>
                                <p class="font-medium text-ink"><?php echo e($lead->created_at->format('d/m/Y')); ?></p>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <a href="<?php echo e(route('admin.leads.show', ['lead' => $lead] + request()->query())); ?>" class="text-sm font-semibold text-pine-700">เปิดดูรายละเอียด</a>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-2xl bg-white p-8 text-center text-pine-700 shadow-sm ring-1 ring-pine-200">ไม่พบข้อมูลลูกค้าตามเงื่อนไขที่เลือก</div>
                <?php endif; ?>
            </div>

            <div class="mt-6">
                <?php echo e($leads->links()); ?>

            </div>
        </div>
    </section>

    <script>
        (() => {
            const board = document.querySelector('[data-kanban-board]');
            if (!board) return;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const message = board.querySelector('[data-kanban-message]');
            let draggedCard = null;

            const setMessage = (text, isError = false) => {
                if (!message) return;
                message.textContent = text;
                message.classList.remove('hidden', 'bg-pine-100', 'text-pine-700', 'bg-rose-50', 'text-rose-700');
                message.classList.add(isError ? 'bg-rose-50' : 'bg-pine-100', isError ? 'text-rose-700' : 'text-pine-700');
            };

            const refreshCounts = () => {
                board.querySelectorAll('[data-kanban-column]').forEach((column) => {
                    const count = column.querySelectorAll('[data-lead-card]').length;
                    const counter = column.querySelector('[data-stage-count]');
                    if (counter) counter.textContent = count;
                });
            };

            const moveLead = async (card, column) => {
                const stage = column.dataset.stage;
                const previousDropzone = card.closest('[data-kanban-dropzone]');
                const dropzone = column.querySelector('[data-kanban-dropzone]');
                const updateUrl = card.dataset.updateUrl;

                if (!stage || !dropzone || !updateUrl || !token) {
                    setMessage('ไม่สามารถย้ายการ์ดได้ กรุณาลองใหม่อีกครั้ง', true);
                    return;
                }

                dropzone.appendChild(card);
                const select = card.querySelector('[data-card-status-select]');
                if (select) select.value = stage;
                refreshCounts();

                try {
                    const response = await fetch(updateUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ lead_status: stage }),
                    });

                    if (!response.ok) throw new Error('Update failed');
                    setMessage('อัปเดตสถานะงานขายเรียบร้อยแล้ว');
                } catch (error) {
                    if (previousDropzone) {
                        previousDropzone.appendChild(card);
                        refreshCounts();
                    }
                    setMessage('อัปเดตสถานะไม่สำเร็จ กรุณาลองอีกครั้ง', true);
                }
            };

            board.querySelectorAll('[data-lead-card]').forEach((card) => {
                card.addEventListener('dragstart', (event) => {
                    draggedCard = card;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', card.dataset.leadId);
                    card.classList.add('opacity-60');
                });

                card.addEventListener('dragend', () => {
                    card.classList.remove('opacity-60');
                    draggedCard = null;
                    board.querySelectorAll('[data-kanban-dropzone]').forEach((zone) => {
                        zone.classList.remove('border-pine-400', 'bg-white');
                    });
                });
            });

            board.querySelectorAll('[data-kanban-column]').forEach((column) => {
                const dropzone = column.querySelector('[data-kanban-dropzone]');
                if (!dropzone) return;

                dropzone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                    dropzone.classList.add('border-pine-400', 'bg-white');
                });

                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-pine-400', 'bg-white');
                });

                dropzone.addEventListener('drop', async (event) => {
                    event.preventDefault();
                    dropzone.classList.remove('border-pine-400', 'bg-white');
                    if (draggedCard) await moveLead(draggedCard, column);
                });
            });
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'CRM Pipeline | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views/admin/leads.blade.php ENDPATH**/ ?>