<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->string('quotation_status')->default('not_started')->after('lead_status');
            $table->date('follow_up_date')->nullable()->after('quotation_status');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropColumn(['quotation_status', 'follow_up_date']);
        });
    }
};
