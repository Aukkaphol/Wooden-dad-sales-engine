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
            $table->string('lead_status')->default('new')->after('status');
        });

        $statusMap = [
            'New' => 'new',
            'Contacted' => 'contacted',
            'Quoted' => 'quoted',
            'Closed' => 'completed',
            'new' => 'new',
        ];

        foreach ($statusMap as $oldStatus => $newStatus) {
            DB::table('leads')->where('status', $oldStatus)->update(['lead_status' => $newStatus]);
        }
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropColumn('lead_status');
        });
    }
};
