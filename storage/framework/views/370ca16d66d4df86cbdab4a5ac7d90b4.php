<?php $__env->startSection('content'); ?>
    <section class="bg-white">
        <div class="mx-auto max-w-md px-5 py-16">
            <h1 class="text-3xl font-semibold">เข้าสู่ระบบแอดมิน</h1>
            <p class="mt-3 text-pine-700">สำหรับดูรายชื่อลูกค้าที่กรอกฟอร์ม</p>

            <form action="<?php echo e(route('login.store')); ?>" method="post" class="mt-8 rounded-lg border border-pine-200 bg-pine-50 p-6">
                <?php echo csrf_field(); ?>

                <label class="block">
                    <span class="text-sm font-semibold">อีเมล</span>
                    <input name="email" type="email" value="<?php echo e(old('email')); ?>" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="mt-5 block">
                    <span class="text-sm font-semibold">รหัสผ่าน</span>
                    <input name="password" type="password" required class="mt-2 w-full rounded-md border border-pine-200 bg-white px-4 py-3 outline-none focus:border-pine-500">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="mt-1 block text-sm text-red-700"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="mt-5 flex items-center gap-2 text-sm text-pine-700">
                    <input name="remember" type="checkbox" value="1" class="rounded border-pine-300">
                    จดจำการเข้าสู่ระบบ
                </label>

                <button type="submit" class="mt-6 w-full rounded-full bg-pine-500 px-6 py-3 font-semibold text-white hover:bg-pine-700">เข้าสู่ระบบ</button>
            </form>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'เข้าสู่ระบบแอดมิน | Wooden Dad Design'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\auth\login.blade.php ENDPATH**/ ?>