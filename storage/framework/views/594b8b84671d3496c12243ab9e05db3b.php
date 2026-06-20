<?php
    $currentStatus = $lead->status ?: $lead->lead_status ?: 'new_lead';
    $timeline = [
        'new_lead' => 'สร้าง Lead',
        'contacted' => 'โทรแล้ว',
        'site_survey' => 'นัดวัดพื้นที่',
        'quotation_sent' => 'ส่งใบเสนอราคา',
        'won' => 'ปิดการขาย',
    ];
    $timelineKeys = array_keys($timeline);
    $currentIndex = array_search($currentStatus, $timelineKeys, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
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
?>

<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <a href="<?php echo e(route('admin.leads.index', request()->query())); ?>" class="text-sm font-semibold text-pine-700 hover:text-ink">กลับไป CRM Pipeline</a>
                    <h1 class="mt-2 text-3xl font-semibold text-ink"><?php echo e($lead->name); ?></h1>
                    <p class="mt-2 text-sm text-pine-700">Lead จาก <?php echo e($lead->source_label); ?> · <?php echo e($lead->created_at->format('d/m/Y H:i')); ?></p>
                </div>
                <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset <?php echo e($statusClasses[$currentStatus] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20'); ?>"><?php echo e($lead->lead_status_label); ?></span>
            </div>

            <?php if(session('success')): ?>
                <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
                <div class="space-y-6">
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ข้อมูลลูกค้า</h2>
                                <p class="mt-1 text-sm text-pine-700">รายละเอียด Lead สำหรับทีมขาย</p>
                            </div>
                            <?php if($lead->phone): ?>
                                <a href="tel:<?php echo e($lead->phone); ?>" class="inline-flex w-fit rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">โทรหาลูกค้า</a>
                            <?php endif; ?>
                        </div>

                        <dl class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div><dt class="text-sm font-medium text-pine-500">ชื่อ</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->name); ?></dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">เบอร์โทร</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->phone ?: '-'); ?></dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">จังหวัด</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->province ?: '-'); ?></dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">งบประมาณ</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->budget ?: '-'); ?></dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">แหล่งที่มา</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->source_label); ?></dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">สถานะใบเสนอราคา</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->quotation_status_label); ?></dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">ขนาดห้อง</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->room_width ?: '-'); ?> x <?php echo e($lead->room_length ?: '-'); ?> ม.</dd></div>
                            <div><dt class="text-sm font-medium text-pine-500">วันติดตาม</dt><dd class="mt-1 font-semibold text-ink"><?php echo e($lead->follow_up_date?->format('d/m/Y') ?? '-'); ?></dd></div>
                        </dl>

                        <div class="mt-6 rounded-xl bg-pine-50 p-4">
                            <p class="text-sm font-medium text-pine-500">ข้อความจากลูกค้า</p>
                            <p class="mt-2 whitespace-pre-line leading-7 text-pine-700"><?php echo e($lead->message ?: 'ลูกค้าไม่ได้ฝากข้อความเพิ่มเติม'); ?></p>
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">Timeline งานขาย</h2>
                        <div class="mt-6 grid gap-3 sm:grid-cols-5">
                            <?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $index = array_search($status, $timelineKeys, true);
                                    $done = $currentStatus === 'won' || $index <= $currentIndex;
                                ?>
                                <div class="rounded-xl p-4 ring-1 <?php echo e($done ? 'bg-pine-700 text-white ring-pine-700' : 'bg-pine-50 text-pine-700 ring-pine-200'); ?>">
                                    <p class="text-xs font-semibold"><?php echo e($loop->iteration); ?></p>
                                    <p class="mt-2 text-sm font-semibold"><?php echo e($label); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">รูปพื้นที่หน้างาน</h2>
                                <p class="mt-1 text-sm text-pine-700">รูปที่ลูกค้าอัปโหลดจากฟอร์ม</p>
                            </div>
                            <?php if($lead->room_image): ?>
                                <a href="<?php echo e(asset('storage/'.$lead->room_image)); ?>" target="_blank" class="text-sm font-semibold text-pine-700 hover:text-ink">เปิดรูปเต็ม</a>
                            <?php endif; ?>
                        </div>
                        <?php if($lead->room_image): ?>
                            <a href="<?php echo e(asset('storage/'.$lead->room_image)); ?>" target="_blank" class="mt-5 block overflow-hidden rounded-2xl ring-1 ring-pine-200">
                                <img src="<?php echo e(asset('storage/'.$lead->room_image)); ?>" alt="รูปพื้นที่ของ <?php echo e($lead->name); ?>" class="aspect-video w-full object-cover">
                            </a>
                        <?php else: ?>
                            <div class="mt-5 rounded-xl border border-dashed border-pine-300 p-8 text-center text-pine-700">ลูกค้ายังไม่ได้อัปโหลดรูปห้อง</div>
                        <?php endif; ?>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-ink">ประวัติใบเสนอราคา</h2>
                                <p class="mt-1 text-sm text-pine-700">ใบเสนอราคาที่เชื่อมกับ Lead นี้</p>
                            </div>
                            <a href="<?php echo e(route('admin.leads.quotations.create', $lead)); ?>" class="inline-flex w-fit rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-500">สร้างใบเสนอราคา</a>
                        </div>
                        <div class="mt-5 grid gap-3">
                            <?php $__empty_1 = true; $__currentLoopData = $lead->quotations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a href="<?php echo e(route('admin.quotations.show', $quotation)); ?>" class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200 hover:bg-pine-100">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-ink"><?php echo e($quotation->quotation_number); ?></p>
                                            <p class="text-sm text-pine-700"><?php echo e($quotation->status_label); ?></p>
                                        </div>
                                        <p class="font-semibold text-ink">฿<?php echo e(number_format((float) ($quotation->grand_total ?: $quotation->subtotal), 2)); ?></p>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="rounded-xl border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีใบเสนอราคา</div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">ใบสั่งผลิต</h2>
                        <div class="mt-5 grid gap-3">
                            <?php $__empty_1 = true; $__currentLoopData = $lead->productionOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a href="<?php echo e(route('admin.production.show', $order)); ?>" class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200 hover:bg-pine-100">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-semibold text-ink"><?php echo e($order->production_order_number); ?></p>
                                            <p class="text-sm text-pine-700"><?php echo e($order->quotation?->quotation_number ?: '-'); ?></p>
                                        </div>
                                        <p class="font-semibold text-pine-700"><?php echo e($order->status_label); ?></p>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="rounded-xl border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีใบสั่งผลิต</div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">Internal Notes</h2>
                        <form action="<?php echo e(route('admin.leads.notes.store', $lead)); ?>" method="post" class="mt-5">
                            <?php echo csrf_field(); ?>
                            <textarea name="note" rows="4" class="w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm leading-6 ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500" placeholder="เพิ่มบันทึกการโทร นัดหมาย หรือข้อกำหนดลูกค้า"><?php echo e(old('note')); ?></textarea>
                            <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <button class="mt-3 rounded-xl bg-pine-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">บันทึกโน้ต</button>
                        </form>

                        <div class="mt-8 space-y-3">
                            <?php $__empty_1 = true; $__currentLoopData = $lead->notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="rounded-xl bg-pine-50 p-4 ring-1 ring-pine-200">
                                    <p class="whitespace-pre-line text-sm leading-6 text-ink"><?php echo e($note->note); ?></p>
                                    <p class="mt-2 text-xs text-pine-600"><?php echo e($note->created_at->format('d/m/Y H:i')); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="rounded-xl border border-dashed border-pine-300 p-6 text-center text-pine-700">ยังไม่มีโน้ตใน Timeline</div>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>

                <aside class="space-y-6">
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <h2 class="text-lg font-semibold text-ink">เปลี่ยนสถานะ</h2>
                        <div class="mt-5 grid gap-2">
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <form action="<?php echo e(route('admin.leads.status', $lead)); ?>" method="post">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="lead_status" value="<?php echo e($value); ?>">
                                    <button class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-semibold ring-1 ring-inset <?php echo e($currentStatus === $value ? ($statusClasses[$value] ?? 'bg-pine-100 text-pine-700 ring-pine-600/20') : 'bg-white text-pine-700 ring-pine-200 hover:bg-pine-50'); ?>">
                                        <span><?php echo e($label); ?></span>
                                        <?php if($currentStatus === $value): ?>
                                            <span class="text-xs">ปัจจุบัน</span>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>

                    <form action="<?php echo e(route('admin.leads.update', $lead)); ?>" method="post" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <h2 class="text-lg font-semibold text-ink">รายละเอียด CRM</h2>
                        <input type="hidden" name="lead_status" value="<?php echo e($currentStatus); ?>">
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">สถานะใบเสนอราคา</span>
                            <select name="quotation_status" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                                <?php $__currentLoopData = $quotationStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php if(old('quotation_status', $lead->quotation_status) === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">วันติดตามลูกค้า</span>
                            <input name="follow_up_date" type="date" value="<?php echo e(old('follow_up_date', $lead->follow_up_date?->format('Y-m-d'))); ?>" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500">
                        </label>
                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-ink">บันทึกภายใน</span>
                            <textarea name="admin_notes" rows="8" class="mt-2 w-full rounded-xl border-0 bg-pine-50 px-3 py-2.5 text-sm leading-6 ring-1 ring-inset ring-pine-200 focus:ring-2 focus:ring-pine-500"><?php echo e(old('admin_notes', $lead->admin_notes)); ?></textarea>
                        </label>
                        <button class="mt-5 w-full rounded-xl bg-pine-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-pine-500">บันทึกข้อมูล CRM</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'รายละเอียด Lead | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\lead-show.blade.php ENDPATH**/ ?>