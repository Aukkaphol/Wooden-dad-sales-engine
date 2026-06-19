<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_orders', function (Blueprint $table): void {
            $table->timestamp('materials_reserved_at')->nullable()->after('delivery_address');
            $table->timestamp('materials_consumed_at')->nullable()->after('materials_reserved_at');
        });
    }

    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table): void {
            $table->dropColumn(['materials_reserved_at', 'materials_consumed_at']);
        });
    }
};
