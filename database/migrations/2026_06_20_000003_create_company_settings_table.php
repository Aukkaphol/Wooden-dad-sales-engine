<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('line_oa_url')->nullable();
            $table->string('line_oa_id')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('website_url')->nullable();
            $table->text('address')->nullable();
            $table->string('province')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('line_channel_id')->nullable();
            $table->string('line_channel_secret')->nullable();
            $table->text('line_channel_access_token')->nullable();
            $table->string('facebook_page_id')->nullable();
            $table->text('facebook_access_token')->nullable();
            $table->string('facebook_webhook_url')->nullable();
            $table->string('google_analytics_measurement_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
