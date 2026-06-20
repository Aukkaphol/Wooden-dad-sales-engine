<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facebook_settings', function (Blueprint $table): void {
            if (! Schema::hasColumn('facebook_settings', 'facebook_app_id')) {
                $table->string('facebook_app_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_app_secret')) {
                $table->string('facebook_app_secret')->nullable()->after('facebook_app_id');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_page_id')) {
                $table->string('facebook_page_id')->nullable()->after('facebook_app_secret');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_page_access_token')) {
                $table->text('facebook_page_access_token')->nullable()->after('facebook_page_id');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_webhook_verify_token')) {
                $table->string('facebook_webhook_verify_token')->nullable()->after('facebook_page_access_token');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_webhook_callback_url')) {
                $table->string('facebook_webhook_callback_url')->nullable()->after('facebook_webhook_verify_token');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_enabled')) {
                $table->boolean('facebook_enabled')->default(false)->after('facebook_webhook_callback_url');
            }

            if (! Schema::hasColumn('facebook_settings', 'facebook_last_synced_at')) {
                $table->timestamp('facebook_last_synced_at')->nullable()->after('facebook_enabled');
            }
        });

        Schema::create('facebook_webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type')->nullable();
            $table->json('payload_json')->nullable();
            $table->string('leadgen_id')->nullable()->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('leads', function (Blueprint $table): void {
            if (! Schema::hasColumn('leads', 'source_platform')) {
                $table->string('source_platform')->nullable()->after('source');
            }

            if (! Schema::hasColumn('leads', 'source_channel')) {
                $table->string('source_channel')->nullable()->after('source_platform');
            }

            if (! Schema::hasColumn('leads', 'ad_name')) {
                $table->string('ad_name')->nullable()->after('campaign_name');
            }

            if (! Schema::hasColumn('leads', 'referrer_url')) {
                $table->string('referrer_url')->nullable()->after('utm_campaign');
            }

            if (! Schema::hasColumn('leads', 'raw_payload_json')) {
                $table->json('raw_payload_json')->nullable()->after('channel_payload');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_webhook_events');

        Schema::table('leads', function (Blueprint $table): void {
            foreach (['source_platform', 'source_channel', 'ad_name', 'referrer_url', 'raw_payload_json'] as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('facebook_settings', function (Blueprint $table): void {
            foreach ([
                'facebook_app_id',
                'facebook_app_secret',
                'facebook_page_id',
                'facebook_page_access_token',
                'facebook_webhook_verify_token',
                'facebook_webhook_callback_url',
                'facebook_enabled',
                'facebook_last_synced_at',
            ] as $column) {
                if (Schema::hasColumn('facebook_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
