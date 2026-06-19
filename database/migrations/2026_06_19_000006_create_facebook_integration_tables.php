<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->nullable();
            $table->string('page_id')->nullable();
            $table->text('page_access_token')->nullable();
            $table->string('app_id')->nullable();
            $table->string('app_secret')->nullable();
            $table->string('webhook_verify_token')->nullable();
            $table->boolean('webhook_enabled')->default(false);
            $table->boolean('lead_ads_enabled')->default(false);
            $table->boolean('messenger_enabled')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('facebook_leads', function (Blueprint $table) {
            $table->id();
            $table->string('facebook_lead_id')->nullable();
            $table->string('page_id')->nullable();
            $table->string('form_id')->nullable();
            $table->string('form_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('province')->nullable();
            $table->string('budget')->nullable();
            $table->string('room_type')->nullable();
            $table->string('room_size')->nullable();
            $table->json('raw_payload')->nullable();
            $table->foreignId('crm_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('status')->default('new');
            $table->timestamps();
        });

        Schema::create('facebook_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->nullable();
            $table->string('page_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('status')->default('received');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('source')->nullable()->after('email');
            $table->string('room_type')->nullable()->after('budget');
            $table->string('room_size')->nullable()->after('room_type');
        });

        DB::statement('ALTER TABLE leads MODIFY phone VARCHAR(255) NULL');
        DB::statement('ALTER TABLE leads MODIFY province VARCHAR(255) NULL');
        DB::statement('ALTER TABLE leads MODIFY budget VARCHAR(255) NULL');
        DB::statement('ALTER TABLE leads MODIFY room_width DECIMAL(6,2) NULL');
        DB::statement('ALTER TABLE leads MODIFY room_length DECIMAL(6,2) NULL');
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['email', 'source', 'room_type', 'room_size']);
        });

        DB::statement("UPDATE leads SET phone = '-' WHERE phone IS NULL");
        DB::statement("UPDATE leads SET province = '-' WHERE province IS NULL");
        DB::statement("UPDATE leads SET budget = '-' WHERE budget IS NULL");
        DB::statement('UPDATE leads SET room_width = 0 WHERE room_width IS NULL');
        DB::statement('UPDATE leads SET room_length = 0 WHERE room_length IS NULL');
        DB::statement('ALTER TABLE leads MODIFY phone VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE leads MODIFY province VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE leads MODIFY budget VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE leads MODIFY room_width DECIMAL(6,2) NOT NULL');
        DB::statement('ALTER TABLE leads MODIFY room_length DECIMAL(6,2) NOT NULL');

        Schema::dropIfExists('facebook_webhook_logs');
        Schema::dropIfExists('facebook_leads');
        Schema::dropIfExists('facebook_settings');
    }
};
