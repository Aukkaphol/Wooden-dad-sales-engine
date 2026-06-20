<?php $__env->startSection('content'); ?>
    <section class="bg-white">
        <div class="mx-auto grid max-w-6xl gap-10 px-5 py-14 lg:grid-cols-[.8fr_1.2fr]">
            <div>
                <p class="text-sm font-semibold text-pine-500">ขอราคาและแบบฟรี</p>
                <h1 class="mt-3 text-4xl font-semibold">เล่าเรื่องห้องของคุณให้เราฟัง</h1>
                <p class="mt-4 leading-8 text-pine-700">กรอกข้อมูลเบื้องต้น ทีม <?php echo e(company()->display_name); ?> จะช่วยประเมินแพ็กเกจ Bedroom Set ที่เหมาะกับพื้นที่ งบประมาณ และสไตล์ที่คุณต้องการ</p>
            </div>

            <form action="<?php echo e(route('lead.store')); ?>" method="post" enctype="multipart/form-data" class="rounded-lg border border-pine-200 bg-pine-50 p-6 shadow-sm">
                <?php echo csrf_field(); ?>

                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold">ชื่อ</span>
                        <input name="name" value="<?php echo e(old('name')); ?>" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">เบอร์โทร</span>
                        <input name="phone" value="<?php echo e(old('phone')); ?>" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">จังหวัด</span>
                        <input name="province" value="<?php echo e(old('province')); ?>" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        <?php $__errorArgs = ['province'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">งบประมาณ</span>
                        <select name="budget" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                            <option value="">เลือกงบประมาณ</option>
                            <?php $__currentLoopData = ['ต่ำกว่า 30,000 บาท', '30,000 - 60,000 บาท', '60,000 - 100,000 บาท', 'มากกว่า 100,000 บาท']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($budget); ?>" <?php if(old('budget') === $budget): echo 'selected'; endif; ?>><?php echo e($budget); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['budget'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">ความกว้างห้อง (เมตร)</span>
                        <input name="room_width" type="number" step="0.01" min="0.1" value="<?php echo e(old('room_width')); ?>" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        <?php $__errorArgs = ['room_width'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">ความยาวห้อง (เมตร)</span>
                        <input name="room_length" type="number" step="0.01" min="0.1" value="<?php echo e(old('room_length')); ?>" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                        <?php $__errorArgs = ['room_length'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>
                </div>

                <label class="mt-5 block">
                    <span class="text-sm font-semibold">ข้อความเพิ่มเติม</span>
                    <textarea name="message" rows="4" class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500"><?php echo e(old('message')); ?></textarea>
                    <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="mt-5 block">
                    <span class="text-sm font-semibold">อัปโหลดรูปห้อง</span>
                    <input name="room_image" type="file" accept="image/*" class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none file:mr-4 file:rounded-full file:border-0 file:bg-pine-100 file:px-4 file:py-2 file:text-pine-700">
                    <?php $__errorArgs = ['room_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <button type="submit" class="mt-6 w-full rounded-full bg-pine-500 px-6 py-3 font-semibold text-white hover:bg-pine-700">ส่งข้อมูลเพื่อขอราคาและแบบฟรี</button>
            </form>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', ['title' => 'ขอราคาและแบบฟรี | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\lead.blade.php ENDPATH**/ ?>