<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            if (! Schema::hasColumn('leads', 'status')) {
                $table->string('status')->default('new_lead')->after('room_image');
            }

            if (! Schema::hasColumn('leads', 'utm_source')) {
                $table->string('utm_source')->nullable()->after('source');
            }

            if (! Schema::hasColumn('leads', 'utm_medium')) {
                $table->string('utm_medium')->nullable()->after('utm_source');
            }

            if (! Schema::hasColumn('leads', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable()->after('utm_medium');
            }

            if (! Schema::hasColumn('leads', 'campaign_name')) {
                $table->string('campaign_name')->nullable()->after('utm_campaign');
            }

            if (! Schema::hasColumn('leads', 'external_lead_id')) {
                $table->string('external_lead_id')->nullable()->after('campaign_name');
            }

            if (! Schema::hasColumn('leads', 'channel_payload')) {
                $table->json('channel_payload')->nullable()->after('external_lead_id');
            }
        });

        DB::statement("UPDATE leads SET status = 'new_lead' WHERE status IS NULL OR status IN ('New', 'new')");
        DB::statement("UPDATE leads SET status = 'quotation_sent' WHERE status IN ('quoted', 'Quoted')");
        DB::statement("UPDATE leads SET status = 'won' WHERE status IN ('completed', 'Closed', 'closed', 'converted')");

        if (Schema::hasColumn('leads', 'lead_status')) {
            DB::statement("UPDATE leads SET lead_status = 'new_lead' WHERE lead_status IS NULL OR lead_status IN ('new', 'New')");
            DB::statement("UPDATE leads SET lead_status = 'quotation_sent' WHERE lead_status IN ('quoted', 'Quoted')");
            DB::statement("UPDATE leads SET lead_status = 'won' WHERE lead_status IN ('completed', 'Closed', 'closed')");
            DB::statement("UPDATE leads SET status = lead_status WHERE lead_status IN ('new_lead', 'contacted', 'site_survey', 'designing', 'quotation_sent', 'negotiation', 'won', 'lost')");
        }

        DB::statement("UPDATE leads SET source = 'website' WHERE source IS NULL OR source = ''");

        try {
            DB::statement("ALTER TABLE leads MODIFY status VARCHAR(255) NOT NULL DEFAULT 'new_lead'");
        } catch (Throwable) {
            // SQLite/test databases do not support MySQL MODIFY syntax.
        }
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            foreach (['utm_source', 'utm_medium', 'utm_campaign', 'campaign_name', 'external_lead_id', 'channel_payload'] as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
