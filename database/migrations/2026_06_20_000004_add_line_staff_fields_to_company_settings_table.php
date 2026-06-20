<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table): void {
            $table->string('line_staff_notify_user_id')->nullable()->after('line_channel_access_token');
            $table->string('line_staff_group_id')->nullable()->after('line_staff_notify_user_id');
        });

        DB::table('company_settings')
            ->where('line_oa_url', 'https://line.me/R/ti/p/@beerklung')
            ->update(['line_oa_url' => null]);
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table): void {
            $table->dropColumn(['line_staff_notify_user_id', 'line_staff_group_id']);
        });
    }
};
