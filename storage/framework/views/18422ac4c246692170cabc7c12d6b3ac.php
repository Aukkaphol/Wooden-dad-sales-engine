<?php
    $statusClasses = [
        'new' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'contacted' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'designing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'quoted' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
        'deposit_paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'production' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'installation' => 'bg-cyan-50 text-cyan-700 ring-cyan-600/20',
        'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
        'lost' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    ];
    $maxMonthly = max(1, $monthlyLeads->max('total') ?? 1);
    $maxProvince = max(1, $provinceStats->max('total') ?? 1);
?>

<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-pine-500">ระบบติดตามงานขาย</p>
                    <h1 class="mt-2 text-3xl font-semibold text-ink">ระบบติดตามงานขาย Wooden Dad Design</h1>
                    <p class="mt-2 text-sm text-pine-700">ดูสถานะลูกค้าชุดห้องนอนตั้งแต่ลีดใหม่จนถึงติดตั้งเสร็จ</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('admin.leads.export', request()->query())); ?>" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 shadow-sm ring-1 ring-inset ring-pine-200 hover:bg-pine-100">ส่งออก Excel</a>
                    <form action="<?php echo e(route('logout')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <button class="inline-flex items-center justify-center rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">ออกจากระบบ</button>
                    </form>
                </div>
            </div>

            <dl class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดวันนี้</dt>
                    <dd class="mt-2 text-3xl font-semibold text-blue-700"><?php echo e(number_format($widgets['leads_today'])); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดเดือนนี้</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700"><?php echo e(number_format($widgets['leads_this_month'])); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">รอติดตามลูกค้า</dt>
                    <dd class="mt-2 text-3xl font-semibold text-rose-700"><?php echo e(number_format($widgets['pending_follow_up'])); ?></dd>
                </div>
            </dl>

            <dl class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ลีดทั้งหมด</dt>
                    <dd class="mt-2 text-3xl font-semibold text-ink"><?php echo e(number_format($kpis['total_leads'])); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ยอดขายคาดการณ์</dt>
                    <dd class="mt-2 text-3xl font-semibold text-pine-700">฿<?php echo e(number_format($kpis['estimated_revenue'])); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">ยอดมัดจำคาดการณ์</dt>
                    <dd class="mt-2 text-3xl font-semibold text-emerald-700">฿<?php echo e(number_format($kpis['deposit_revenue'])); ?></dd>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                    <dt class="text-sm font-medium text-pine-700">อัตราปิดการขาย</dt>
                    <dd class="mt-2 text-3xl font-semibold text-green-700"><?php echo e($kpis['conversion_rate']); ?>%</dd>
                </div>
            </dl>

            <div class="mt-8 grid gap-6 xl:grid-cols-[1.15fr_.85fr_.7fr]">
                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">ลีดรายเดือน</h2>
                            <p class="mt-1 text-sm text-pine-700">จำนวนลีดที่เข้ามาในแต่ละเดือน</p>
                        </div>
                    </div>
                    <div class="mt-6 flex h-56 items-end gap-3 overflow-x-auto pb-2">
                        <?php $__empty_1 = true; $__currentLoopData = $monthlyLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex min-w-16 flex-1 flex-col items-center gap-2">
                                <div class="flex h-40 w-full items-end rounded-md bg-pine-50 px-2">
                                    <div class="w-full rounded-t-md bg-pine-500" style="height: <?php echo e(max(8, ($month->total / $maxMonthly) * 100)); ?>%"></div>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-ink"><?php echo e($month->total); ?></p>
                                    <p class="text-xs text-pine-600"><?php echo e($month->month); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="flex h-full w-full items-center justify-center rounded-md border border-dashed border-pine-300 text-pine-700">ยังไม่มีข้อมูลสำหรับทำกราฟ</div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">อัตราปิดการขาย</h2>
                    <p class="mt-1 text-sm text-pine-700">งานที่ปิดสำเร็จเทียบกับลีดทั้งหมด</p>
                    <div class="mt-6 flex items-center justify-center">
                        <div class="flex h-36 w-36 items-center justify-center rounded-full bg-pine-100 ring-8 ring-white">
                            <div class="text-center">
                                <p class="text-4xl font-semibold text-pine-700"><?php echo e($conversionRate); ?>%</p>
                                <p class="mt-1 text-xs text-pine-600">ปิดงานแล้ว</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 h-3 overflow-hidden rounded-full bg-pine-100">
                        <div class="h-full rounded-full bg-pine-500" style="width: <?php echo e(min(100, $conversionRate)); ?>%"></div>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-pine-200">
                    <h2 class="text-lg font-semibold text-ink">สถิติตามจังหวัด</h2>
                    <p class="mt-1 text-sm text-pine-700">จังหวัดที่มีลีดมากที่สุด</p>
                    <div class="mt-6 space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $provinceStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div>
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="truncate font-medium text-ink"><?php echo e($province->province); ?></span>
                                    <span class="text-pine-700"><?php echo e($province->total); ?></span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-pine-100">
                                    <div class="h-full rounded-full bg-pine-500" style="width: <?php echo e(max(8, ($province->total / $maxProvince) * 100)); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-md border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีข้อมูลจังหวัด</div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <section class="mt-8 rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200" data-kanban-board>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-ink">บอร์ดงานขาย CRM</h2>
                        <p class="mt-1 text-sm text-pine-700">ลากการ์ดเพื่อย้ายขั้นตอน หรือใช้ปุ่มย้ายบนการ์ดลูกค้า</p>
                    </div>
                    <p class="hidden rounded-md bg-pine-100 px-3 py-2 text-sm font-semibold text-pine-700" data-kanban-message></p>
                </div>
                <div class="mt-5 grid gap-4 xl:grid-cols-5">
                    <?php $__currentLoopData = $kanbanStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg bg-pine-50 p-3 ring-1 ring-pine-200" data-kanban-column data-stage="<?php echo e($stage); ?>">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="font-semibold text-ink"><?php echo e($label); ?></h3>
                                <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-pine-700 ring-1 ring-pine-200" data-stage-count><?php echo e(($kanbanLeads[$stage] ?? collect())->count()); ?></span>
                            </div>
                            <div class="mt-4 min-h-32 space-y-3 rounded-md border border-dashed border-transparent p-1 transition" data-kanban-dropzone>
                                <?php $__empty_1 = true; $__currentLoopData = ($kanbanLeads[$stage] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <article
                                        class="cursor-grab rounded-md bg-white p-3 shadow-sm ring-1 ring-pine-200 transition active:cursor-grabbing"
                                        draggable="true"
                                        data-lead-card
                                        data-lead-id="<?php echo e($lead->id); ?>"
                                        data-update-url="<?php echo e(route('admin.leads.status', $lead)); ?>"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <a href="<?php echo e(route('admin.leads.show', ['lead' => $lead] + request()->query())); ?>" class="block truncate text-sm font-semibold text-ink hover:text-pine-700"><?php echo e($lead->name); ?></a>
                                                <p class="mt-1 text-xs text-pine-700"><?php echo e($lead->phone); ?></p>
                                            </div>
                                            <?php if($lead->room_image): ?>
                                                <img src="<?php echo e(asset('storage/'.$lead->room_image)); ?>" alt="รูปห้องของ <?php echo e($lead->name); ?>" class="h-10 w-12 shrink-0 rounded object-cover ring-1 ring-pine-200">
                                            <?php endif; ?>
                                        </div>
                                        <p class="mt-3 truncate text-xs text-pine-700"><?php echo e($lead->province); ?> · <?php echo e($lead->budget); ?></p>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-pine-100 px-2 py-1 font-semibold text-pine-700"><?php echo e($lead->quotation_status_label); ?></span>
                                            <?php if($lead->follow_up_date): ?>
                                                <span class="rounded-full bg-white px-2 py-1 font-semibold text-pine-700 ring-1 ring-pine-200"><?php echo e($lead->follow_up_date->format('d/m/Y')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <form action="<?php echo e(route('admin.leads.status', $lead)); ?>" method="post" class="mt-3 flex gap-2">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <select name="lead_status" data-card-status-select class="min-w-0 flex-1 rounded-md border-0 bg-pine-50 px-2 py-1.5 text-xs ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                                <?php $__currentLoopData = $kanbanStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $stageLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($value); ?>" <?php if($lead->lead_status === $value): echo 'selected'; endif; ?>><?php echo e($stageLabel); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <button class="rounded-md bg-pine-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-pine-500">ย้าย</button>
                                        </form>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="rounded-md border border-dashed border-pine-300 p-4 text-center text-sm text-pine-700">ไม่มีลีด</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <form method="get" action="<?php echo e(route('admin.leads.index')); ?>" class="mt-8 rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                    <label class="xl:col-span-2">
                        <span class="text-sm font-semibold text-ink">ค้นหาชื่อหรือเบอร์โทร</span>
                        <input name="search" value="<?php echo e($filters['search'] ?? ''); ?>" placeholder="เช่น สมชาย หรือ 081..." class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 placeholder:text-pine-400 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">จังหวัด</span>
                        <select name="province" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกจังหวัด</option>
                            <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($province); ?>" <?php if(($filters['province'] ?? '') === $province): echo 'selected'; endif; ?>><?php echo e($province); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">สถานะ CRM</span>
                        <select name="lead_status" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                            <option value="">ทุกสถานะ</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php if(($filters['lead_status'] ?? '') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ตั้งแต่วันที่</span>
                        <input name="date_from" type="date" value="<?php echo e($filters['date_from'] ?? ''); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                    <label>
                        <span class="text-sm font-semibold text-ink">ถึงวันที่</span>
                        <input name="date_to" type="date" value="<?php echo e($filters['date_to'] ?? ''); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                    </label>
                </div>
                <div class="mt-5 flex flex-wrap gap-3">
                    <button class="rounded-md bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">ค้นหา</button>
                    <a href="<?php echo e(route('admin.leads.index')); ?>" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-inset ring-pine-200 hover:bg-pine-100">ล้างตัวกรอง</a>
                </div>
            </form>

            <div class="mt-8 hidden overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-pine-200 lg:block">
                <table class="min-w-full divide-y divide-pine-200">
                    <thead class="bg-pine-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">ลูกค้า</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">จังหวัด</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">งบประมาณ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">ขนาดห้อง</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">สถานะ CRM</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">รูป</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-pine-700">วันที่</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-pine-700">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pine-100 bg-white">
                        <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-pine-50">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-ink"><?php echo e($lead->name); ?></div>
                                    <div class="mt-1 text-sm text-pine-700"><?php echo e($lead->phone); ?></div>
                                </td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->province); ?></td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->budget); ?></td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->room_width); ?> x <?php echo e($lead->room_length); ?> ม.</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?php echo e($statusClasses[$lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20'); ?>"><?php echo e($lead->lead_status_label); ?></span>
                                </td>
                                <td class="px-4 py-4">
                                    <?php if($lead->room_image): ?>
                                        <img src="<?php echo e(asset('storage/'.$lead->room_image)); ?>" alt="รูปห้องของ <?php echo e($lead->name); ?>" class="h-12 w-16 rounded-md object-cover ring-1 ring-pine-200">
                                    <?php else: ?>
                                        <span class="text-sm text-pine-400">ไม่มีรูป</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-sm text-pine-700"><?php echo e($lead->created_at->format('d/m/Y H:i')); ?></td>
                                <td class="px-4 py-4 text-right">
                                    <a href="<?php echo e(route('admin.leads.show', ['lead' => $lead] + request()->query())); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">ดูรายละเอียด</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-pine-700">ไม่พบข้อมูลลูกค้าตามเงื่อนไขที่เลือก</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 space-y-4 lg:hidden">
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="font-semibold text-ink"><?php echo e($lead->name); ?></h2>
                                <p class="mt-1 text-sm text-pine-700"><?php echo e($lead->phone); ?> · <?php echo e($lead->province); ?></p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset <?php echo e($statusClasses[$lead->lead_status] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20'); ?>"><?php echo e($lead->lead_status_label); ?></span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-pine-500">งบประมาณ</p>
                                <p class="font-medium text-ink"><?php echo e($lead->budget); ?></p>
                            </div>
                            <div>
                                <p class="text-pine-500">ขนาดห้อง</p>
                                <p class="font-medium text-ink"><?php echo e($lead->room_width); ?> x <?php echo e($lead->room_length); ?> ม.</p>
                            </div>
                        </div>
                        <?php if($lead->room_image): ?>
                            <img src="<?php echo e(asset('storage/'.$lead->room_image)); ?>" alt="รูปห้องของ <?php echo e($lead->name); ?>" class="mt-4 aspect-video w-full rounded-md object-cover ring-1 ring-pine-200">
                        <?php endif; ?>
                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-sm text-pine-700"><?php echo e($lead->created_at->format('d/m/Y H:i')); ?></p>
                            <a href="<?php echo e(route('admin.leads.show', ['lead' => $lead] + request()->query())); ?>" class="text-sm font-semibold text-pine-700">ดูรายละเอียด</a>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-lg bg-white p-8 text-center text-pine-700 shadow-sm ring-1 ring-pine-200">ไม่พบข้อมูลลูกค้าตามเงื่อนไขที่เลือก</div>
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
            if (!board) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const message = board.querySelector('[data-kanban-message]');
            let draggedCard = null;

            const setMessage = (text, isError = false) => {
                if (!message) {
                    return;
                }

                message.textContent = text;
                message.classList.remove('hidden', 'bg-pine-100', 'text-pine-700', 'bg-rose-50', 'text-rose-700');
                message.classList.add(isError ? 'bg-rose-50' : 'bg-pine-100', isError ? 'text-rose-700' : 'text-pine-700');
            };

            const refreshCounts = () => {
                board.querySelectorAll('[data-kanban-column]').forEach((column) => {
                    const count = column.querySelectorAll('[data-lead-card]').length;
                    const counter = column.querySelector('[data-stage-count]');
                    if (counter) {
                        counter.textContent = count;
                    }
                });
            };

            const moveLead = async (card, column) => {
                const stage = column.dataset.stage;
                const previousDropzone = card.closest('[data-kanban-dropzone]');
                const dropzone = column.querySelector('[data-kanban-dropzone]');
                const updateUrl = card.dataset.updateUrl;

                if (!stage || !dropzone || !updateUrl || !token) {
                    setMessage('ไม่สามารถย้ายการ์ดได้ เนื่องจากตั้งค่าบอร์ดไม่ครบ', true);
                    return;
                }

                dropzone.appendChild(card);
                const select = card.querySelector('[data-card-status-select]');
                if (select) {
                    select.value = stage;
                }
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

                    if (!response.ok) {
                        throw new Error('อัปเดตสถานะไม่สำเร็จ');
                    }

                    setMessage('อัปเดตขั้นตอนงานขายเรียบร้อยแล้ว');
                } catch (error) {
                    if (previousDropzone) {
                        previousDropzone.appendChild(card);
                        refreshCounts();
                    }
                    setMessage('อัปเดตขั้นตอนงานขายไม่สำเร็จ กรุณาลองอีกครั้ง', true);
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
                if (!dropzone) {
                    return;
                }

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
                    if (draggedCard) {
                        await moveLead(draggedCard, column);
                    }
                });
            });
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'ระบบ CRM | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\leads.blade.php ENDPATH**/ ?>