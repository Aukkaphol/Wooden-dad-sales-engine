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
            $table->text('admin_notes')->nullable()->after('status');
        });

        DB::table('leads')->where('status', 'new')->update(['status' => 'New']);
        DB::statement("ALTER TABLE leads MODIFY status VARCHAR(255) NOT NULL DEFAULT 'New'");
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropColumn('admin_notes');
        });

        DB::statement("ALTER TABLE leads MODIFY status VARCHAR(255) NOT NULL DEFAULT 'new'");
    }
};
