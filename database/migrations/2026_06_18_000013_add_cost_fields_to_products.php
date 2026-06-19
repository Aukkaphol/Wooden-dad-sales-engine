<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('labor_cost', 12, 2)->default(0)->after('description');
            $table->decimal('finishing_cost', 12, 2)->default(0)->after('labor_cost');
            $table->decimal('hardware_cost', 12, 2)->default(0)->after('finishing_cost');
        });

        DB::table('products')
            ->where('name', 'Bed 6 ft')
            ->update([
                'labor_cost' => 1800,
                'finishing_cost' => 950,
                'hardware_cost' => 450,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['labor_cost', 'finishing_cost', 'hardware_cost']);
        });
    }
};
