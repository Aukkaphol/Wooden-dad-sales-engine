<?php $__env->startSection('content'); ?>
<section class="bg-pine-50">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-semibold text-pine-500">Company Profile & Integrations</p>
            <h1 class="mt-2 text-3xl font-semibold text-ink">ตั้งค่าข้อมูลบริษัท</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-pine-700">จัดการข้อมูลแบรนด์ ช่องทางติดต่อ โซเชียลมีเดีย โลโก้ และข้อมูลเชื่อมต่อระบบกลางสำหรับทุกโมดูลของ <?php echo e(company()->display_name); ?> ERP</p>
        </div>

        <?php if(session('success')): ?>
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800 ring-1 ring-green-600/20"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-800 ring-1 ring-rose-600/20"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <div class="mb-6 overflow-x-auto rounded-2xl bg-white p-2 shadow-sm ring-1 ring-pine-200">
            <div class="flex min-w-max gap-2 text-sm font-semibold text-pine-700">
                <a href="#company" class="rounded-xl px-4 py-2.5 hover:bg-pine-50">ข้อมูลบริษัท</a>
                <a href="#contact" class="rounded-xl px-4 py-2.5 hover:bg-pine-50">ข้อมูลติดต่อ</a>
                <a href="#social" class="rounded-xl px-4 py-2.5 hover:bg-pine-50">โซเชียลมีเดีย</a>
                <a href="#branding" class="rounded-xl px-4 py-2.5 hover:bg-pine-50">แบรนด์ดิ้ง</a>
                <a href="#integrations" class="rounded-xl px-4 py-2.5 hover:bg-pine-50">การเชื่อมต่อระบบ</a>
            </div>
        </div>

        <form method="post" action="<?php echo e(route('admin.settings.company.update')); ?>" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <section id="company" class="scroll-mt-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <div class="mb-5">
                    <h2 class="text-xl font-semibold text-ink">ข้อมูลบริษัท</h2>
                    <p class="mt-1 text-sm text-pine-700">ข้อมูลหลักที่ใช้ในเอกสาร ใบเสนอราคา และหน้าเว็บ</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <label><span class="text-sm font-semibold text-ink">Company Name</span><input name="company_name" value="<?php echo e(old('company_name', $company->company_name)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">Brand Name</span><input name="brand_name" value="<?php echo e(old('brand_name', $company->brand_name)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">Tax ID</span><input name="tax_id" value="<?php echo e(old('tax_id', $company->tax_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">Province</span><input name="province" value="<?php echo e(old('province', $company->province)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">Address</span><textarea name="address" rows="3" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('address', $company->address)); ?></textarea></label>
                    <label class="md:col-span-2"><span class="text-sm font-semibold text-ink">Website URL</span><input name="website_url" value="<?php echo e(old('website_url', $company->website_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                </div>
            </section>

            <section id="contact" class="scroll-mt-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <div class="mb-5">
                    <h2 class="text-xl font-semibold text-ink">ข้อมูลติดต่อ</h2>
                    <p class="mt-1 text-sm text-pine-700">ข้อมูลที่ใช้ใน footer, LINE CTA, PDF และหน้าติดต่อ</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <label><span class="text-sm font-semibold text-ink">Phone</span><input name="phone" value="<?php echo e(old('phone', $company->phone)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">Email</span><input name="email" value="<?php echo e(old('email', $company->email)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">LINE OA ID</span><input name="line_oa_id" value="<?php echo e(old('line_oa_id', $company->line_oa_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">LINE OA URL</span><input name="line_oa_url" value="<?php echo e(old('line_oa_url', $company->line_oa_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                </div>
            </section>

            <section id="social" class="scroll-mt-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <div class="mb-5">
                    <h2 class="text-xl font-semibold text-ink">โซเชียลมีเดีย</h2>
                    <p class="mt-1 text-sm text-pine-700">ลิงก์ช่องทางการตลาดและช่องทางพิสูจน์ผลงานของแบรนด์</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <label><span class="text-sm font-semibold text-ink">Facebook URL</span><input name="facebook_url" value="<?php echo e(old('facebook_url', $company->facebook_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">Instagram URL</span><input name="instagram_url" value="<?php echo e(old('instagram_url', $company->instagram_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">TikTok URL</span><input name="tiktok_url" value="<?php echo e(old('tiktok_url', $company->tiktok_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">YouTube URL</span><input name="youtube_url" value="<?php echo e(old('youtube_url', $company->youtube_url)); ?>" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                </div>
            </section>

            <section id="branding" class="scroll-mt-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <div class="mb-5">
                    <h2 class="text-xl font-semibold text-ink">แบรนด์ดิ้ง</h2>
                    <p class="mt-1 text-sm text-pine-700">โลโก้ สีหลัก และสีรองสำหรับประสบการณ์แบรนด์ <?php echo e(company()->display_name); ?></p>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-semibold text-ink">Upload Logo</span>
                        <input name="logo" type="file" accept="image/*" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        <?php if($company->logo_url): ?>
                            <img src="<?php echo e($company->logo_url); ?>" alt="Company Logo" class="mt-3 h-16 w-auto rounded-md bg-white object-contain ring-1 ring-pine-200">
                        <?php endif; ?>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-ink">Upload Favicon</span>
                        <input name="favicon" type="file" accept="image/*,.ico" class="mt-2 w-full rounded-md border-0 bg-pine-50 px-3 py-2.5 text-sm ring-1 ring-pine-200">
                        <?php if($company->favicon_url): ?>
                            <img src="<?php echo e($company->favicon_url); ?>" alt="Favicon" class="mt-3 h-12 w-12 rounded-md bg-white object-contain ring-1 ring-pine-200">
                        <?php endif; ?>
                    </label>
                    <label><span class="text-sm font-semibold text-ink">Primary Color</span><input name="primary_color" type="color" value="<?php echo e(old('primary_color', $company->primary_color ?: '#7a5634')); ?>" class="mt-2 h-12 w-full rounded-md border-0 bg-pine-50 px-2 py-1 ring-1 ring-pine-200"></label>
                    <label><span class="text-sm font-semibold text-ink">Secondary Color</span><input name="secondary_color" type="color" value="<?php echo e(old('secondary_color', $company->secondary_color ?: '#f6ead8')); ?>" class="mt-2 h-12 w-full rounded-md border-0 bg-pine-50 px-2 py-1 ring-1 ring-pine-200"></label>
                </div>
            </section>

            <section id="integrations" class="scroll-mt-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-pine-200">
                <div class="mb-5">
                    <h2 class="text-xl font-semibold text-ink">การเชื่อมต่อระบบ</h2>
                    <p class="mt-1 text-sm text-pine-700">เก็บค่า integration กลางสำหรับ LINE OA, Facebook และ Google Analytics</p>
                </div>
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="rounded-xl bg-pine-50 p-5 ring-1 ring-pine-200">
                        <h3 class="font-semibold text-ink">LINE OA</h3>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Channel ID</span><input name="line_channel_id" value="<?php echo e(old('line_channel_id', $company->line_channel_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Channel Secret</span><input name="line_channel_secret" type="password" value="<?php echo e(old('line_channel_secret', $company->line_channel_secret)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Channel Access Token</span><textarea name="line_channel_access_token" rows="4" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('line_channel_access_token', $company->line_channel_access_token)); ?></textarea></label>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Staff User ID</span><input name="line_staff_notify_user_id" value="<?php echo e(old('line_staff_notify_user_id', $company->line_staff_notify_user_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Staff Group ID</span><input name="line_staff_group_id" value="<?php echo e(old('line_staff_group_id', $company->line_staff_group_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    </div>
                    <div class="rounded-xl bg-pine-50 p-5 ring-1 ring-pine-200">
                        <h3 class="font-semibold text-ink">Facebook</h3>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Page ID</span><input name="facebook_page_id" value="<?php echo e(old('facebook_page_id', $company->facebook_page_id)); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Access Token</span><textarea name="facebook_access_token" rows="4" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"><?php echo e(old('facebook_access_token', $company->facebook_access_token)); ?></textarea></label>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Webhook URL</span><input name="facebook_webhook_url" value="<?php echo e(old('facebook_webhook_url', $company->facebook_webhook_url ?: route('webhooks.facebook.receive'))); ?>" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    </div>
                    <div class="rounded-xl bg-pine-50 p-5 ring-1 ring-pine-200">
                        <h3 class="font-semibold text-ink">Google Analytics</h3>
                        <label class="mt-4 block"><span class="text-sm font-semibold text-ink">Measurement ID</span><input name="google_analytics_measurement_id" value="<?php echo e(old('google_analytics_measurement_id', $company->google_analytics_measurement_id)); ?>" placeholder="G-XXXXXXXXXX" class="mt-2 w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm ring-1 ring-pine-200"></label>
                    </div>
                </div>
            </section>

            <div class="sticky bottom-4 z-10 flex justify-end">
                <button class="rounded-full bg-pine-700 px-7 py-3 text-sm font-semibold text-white shadow-lg shadow-pine-900/20 hover:bg-pine-500">บันทึกข้อมูลบริษัท</button>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', ['title' => 'ตั้งค่าข้อมูลบริษัท | '.company()->display_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\BEER\Documents\Codex\2026-06-17\create-a-laravel-12-project-named\wooden-dad-sales-engine\resources\views\admin\settings\company.blade.php ENDPATH**/ ?>