<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php
        $company = company();
    ?>
    <title><?php echo e($title ?? $company->display_name.' Sales CRM'); ?></title>
    <?php if($company->favicon_url): ?>
        <link rel="icon" href="<?php echo e($company->favicon_url); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-thai:400,500,600,700" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-pine-50 text-ink antialiased">
    <?php
        $menuGroups = [
            'Dashboard' => [
                ['label' => 'แดชบอร์ด', 'route' => 'admin.dashboard'],
            ],
            'Marketing' => [
                ['label' => 'Overview', 'route' => 'admin.dashboard'],
                ['label' => 'Website Leads', 'route' => 'admin.marketing.website-leads'],
                ['label' => 'Facebook Leads', 'route' => 'admin.marketing.facebook-leads'],
                ['label' => 'LINE OA Leads', 'route' => 'admin.marketing.line-leads'],
                ['label' => 'Reviews', 'route' => 'admin.marketing.reviews.index'],
                ['label' => 'Portfolio', 'route' => 'admin.portfolio.index'],
                ['label' => 'Campaigns', 'route' => 'admin.marketing.campaigns'],
                ['label' => 'Analytics', 'route' => 'admin.marketing.analytics'],
            ],
            'Sales' => [
                ['label' => 'CRM Pipeline', 'route' => 'admin.leads.index'],
                ['label' => 'Quotations (QTN)', 'route' => 'admin.quotations.index'],
                ['label' => 'Customers', 'route' => 'admin.leads.index'],
            ],
            'Production' => [
                ['label' => 'Production Orders (PO)', 'route' => 'admin.production.index'],
                ['label' => 'Work Schedule', 'route' => 'admin.production.index'],
                ['label' => 'Installation Schedule', 'route' => 'admin.installation.index'],
            ],
            'Inventory' => [
                ['label' => 'Products', 'route' => 'admin.products.index'],
                ['label' => 'BOM', 'route' => 'admin.products.index'],
                ['label' => 'Material Stock', 'route' => 'admin.materials.index'],
                ['label' => 'Purchase Requests', 'route' => 'admin.purchase-requests.index'],
            ],
            'Settings' => [
                ['label' => 'Company Profile', 'route' => 'admin.settings.company.edit', 'anchor' => 'company'],
                ['label' => 'Social Media', 'route' => 'admin.settings.company.edit', 'anchor' => 'social'],
                ['label' => 'LINE OA', 'route' => 'admin.settings.line.edit'],
                ['label' => 'Facebook', 'route' => 'admin.settings.facebook.edit'],
                ['label' => 'Website', 'route' => 'admin.marketing.homepage'],
                ['label' => 'Users & Roles', 'route' => 'admin.settings.users-roles'],
            ],
        ];
    ?>

    <div class="min-h-screen lg:grid lg:grid-cols-[292px_1fr]">
        <aside class="sticky top-0 hidden h-screen overflow-y-auto border-r border-pine-200 bg-white/95 px-4 py-5 shadow-[10px_0_40px_rgba(93,66,39,.06)] backdrop-blur lg:block">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 rounded-2xl bg-pine-50 p-3">
                <?php if($company->logo_url): ?>
                    <img src="<?php echo e($company->logo_url); ?>" alt="<?php echo e($company->display_name); ?>" class="h-11 w-11 rounded-full bg-white object-contain ring-1 ring-pine-200">
                <?php else: ?>
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-pine-700 text-sm font-semibold text-white">WD</span>
                <?php endif; ?>
                <span class="min-w-0">
                    <span class="block truncate font-semibold text-ink"><?php echo e($company->display_name); ?></span>
                    <span class="block text-xs font-medium text-pine-700">Sales CRM + Marketing</span>
                </span>
            </a>

            <nav class="mt-6 space-y-6">
                <?php $__currentLoopData = $menuGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <section>
                        <p class="px-3 text-xs font-semibold uppercase tracking-wide text-pine-500"><?php echo e($group); ?></p>
                        <div class="mt-2 grid gap-1">
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $href = route($item['route']).(isset($item['anchor']) ? '#'.$item['anchor'] : '');
                                    $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
                                ?>
                                <a href="<?php echo e($href); ?>" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50 hover:text-ink <?php echo e($active ? 'bg-pine-100 text-ink' : ''); ?>"><?php echo e($item['label']); ?></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-40 border-b border-pine-200 bg-white/86 backdrop-blur-xl">
                <div class="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <details class="group relative lg:hidden">
                        <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl bg-pine-50 text-pine-800 ring-1 ring-pine-200 marker:hidden" aria-label="เปิดเมนูแอดมิน">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-open:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5 group-open:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </summary>
                        <div class="absolute left-0 top-14 max-h-[78vh] w-[min(88vw,360px)] overflow-y-auto rounded-2xl border border-pine-100 bg-white p-4 shadow-[0_24px_70px_rgba(28,23,18,.20)]">
                            <?php $__currentLoopData = $menuGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <section class="mb-4">
                                    <p class="px-2 text-xs font-semibold uppercase tracking-wide text-pine-500"><?php echo e($group); ?></p>
                                    <div class="mt-2 grid gap-1">
                                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route($item['route']).(isset($item['anchor']) ? '#'.$item['anchor'] : '')); ?>" class="rounded-xl px-3 py-2.5 text-sm font-semibold text-pine-700 hover:bg-pine-50"><?php echo e($item['label']); ?></a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </section>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </details>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-pine-500"><?php echo e($company->display_name); ?></p>
                        <h1 class="truncate text-lg font-semibold text-ink"><?php echo e($title ?? 'แดชบอร์ด'); ?></h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-ink"><?php echo e(auth()->user()->name ?? 'Admin'); ?></p>
                            <p class="text-xs text-pine-600">ผู้ดูแลระบบ</p>
                        </div>
                        <form method="post" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-pine-700 ring-1 ring-pine-200 hover:bg-pine-50">ออกจากระบบ</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="min-w-0">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\layouts\admin.blade.php ENDPATH**/ ?>