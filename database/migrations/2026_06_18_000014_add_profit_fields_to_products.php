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
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('products', 'selling_price')) {
                $table->decimal('selling_price', 12, 2)->default(0)->after('description');
            }

            if (! Schema::hasColumn('products', 'material_cost')) {
                $table->decimal('material_cost', 12, 2)->default(0)->after('selling_price');
            }

            if (! Schema::hasColumn('products', 'other_cost')) {
                $table->decimal('other_cost', 12, 2)->default(0)->after('finishing_cost');
            }

            if (! Schema::hasColumn('products', 'total_cost')) {
                $table->decimal('total_cost', 12, 2)->default(0)->after('other_cost');
            }

            if (! Schema::hasColumn('products', 'profit_amount')) {
                $table->decimal('profit_amount', 12, 2)->default(0)->after('total_cost');
            }

            if (! Schema::hasColumn('products', 'profit_percent')) {
                $table->decimal('profit_percent', 8, 2)->default(0)->after('profit_amount');
            }
        });

        DB::table('products')->orderBy('id')->get()->each(function (object $product, int $index): void {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'sku' => $product->sku ?: 'WD-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'selling_price' => $product->selling_price ?: DB::table('quotation_items')
                        ->whereRaw('LOWER(product_name) = ?', [strtolower($product->name)])
                        ->avg('unit_price') ?: 0,
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            foreach (['sku', 'selling_price', 'material_cost', 'other_cost', 'total_cost', 'profit_amount', 'profit_percent'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
