<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'product_image')) {
                $table->string('product_image')->nullable()->after('sku');
            }

            if (! Schema::hasColumn('products', 'category')) {
                $table->string('category')->nullable()->after('description');
            }

            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('profit_percent');
            }
        });

        Schema::table('production_orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('production_orders', 'installation_date')) {
                $table->date('installation_date')->nullable()->after('delivery_date');
            }

            if (! Schema::hasColumn('production_orders', 'installation_status')) {
                $table->string('installation_status')->default('pending')->after('installation_date');
            }

            if (! Schema::hasColumn('production_orders', 'material_cost')) {
                $table->decimal('material_cost', 12, 2)->default(0)->after('delivery_address');
            }

            if (! Schema::hasColumn('production_orders', 'labor_cost')) {
                $table->decimal('labor_cost', 12, 2)->default(0)->after('material_cost');
            }

            if (! Schema::hasColumn('production_orders', 'delivery_cost')) {
                $table->decimal('delivery_cost', 12, 2)->default(0)->after('labor_cost');
            }

            if (! Schema::hasColumn('production_orders', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->default(0)->after('delivery_cost');
            }

            if (! Schema::hasColumn('production_orders', 'gross_margin')) {
                $table->decimal('gross_margin', 8, 2)->default(0)->after('total_cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('production_orders', function (Blueprint $table): void {
            $table->dropColumn([
                'installation_date',
                'installation_status',
                'material_cost',
                'labor_cost',
                'delivery_cost',
                'total_cost',
                'gross_margin',
            ]);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['product_image', 'category', 'is_active']);
        });
    }
};
