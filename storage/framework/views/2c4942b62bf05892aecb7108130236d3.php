<?php $__env->startSection('content'); ?>
    <section class="bg-pine-50">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-pine-200">
                <p class="text-sm font-semibold text-pine-500">Settings</p>
                <h1 class="mt-2 text-3xl font-semibold text-ink">Users & Roles</h1>
                <p class="mt-3 text-sm leading-6 text-pine-700">พื้นที่เตรียมจัดการผู้ใช้งานและสิทธิ์การเข้าถึง สำหรับขยายทีมขาย ทีมการตลาด ทีมผลิต และผู้บริหารในอนาคต</p>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'Users & Roles | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\settings\users-roles.blade.php ENDPATH**/ ?>