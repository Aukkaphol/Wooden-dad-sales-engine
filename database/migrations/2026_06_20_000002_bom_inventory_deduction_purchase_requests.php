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
            if (! Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('ชิ้น')->after('category');
            }

            if (! Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->default(0)->after('selling_price');
            }

            if (! Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('cost_price');
            }
        });

        Schema::table('materials', function (Blueprint $table): void {
            if (! Schema::hasColumn('materials', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('materials', 'minimum_stock')) {
                $table->decimal('minimum_stock', 12, 3)->default(0)->after('current_stock');
            }

            if (! Schema::hasColumn('materials', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->default(0)->after('minimum_stock');
            }

            if (! Schema::hasColumn('materials', 'supplier_name')) {
                $table->string('supplier_name')->nullable()->after('cost_price');
            }
        });

        Schema::table('bom_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('bom_items', 'qty_required')) {
                $table->decimal('qty_required', 12, 3)->default(0)->after('material_id');
            }

            if (! Schema::hasColumn('bom_items', 'waste_percent')) {
                $table->decimal('waste_percent', 8, 2)->default(0)->after('qty_required');
            }
        });

        Schema::create('production_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('qty', 12, 3)->default(1);
            $table->string('unit')->default('ชิ้น');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('qty', 12, 3);
            $table->decimal('before_stock', 12, 3)->default(0);
            $table->decimal('after_stock', 12, 3)->default(0);
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('pr_no')->unique();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->foreignId('production_order_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('requested_qty', 12, 3);
            $table->text('reason')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        DB::table('materials')->orderBy('id')->get()->each(function (object $material, int $index): void {
            DB::table('materials')
                ->where('id', $material->id)
                ->update([
                    'sku' => $material->sku ?: 'MAT-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'minimum_stock' => $material->minimum_stock ?: ($material->low_stock_level ?? 0),
                    'cost_price' => $material->cost_price ?: ($material->unit_cost ?? 0),
                    'updated_at' => now(),
                ]);
        });

        DB::table('products')->whereNull('unit')->update(['unit' => 'ชิ้น']);
        DB::table('products')->where('cost_price', 0)->update(['cost_price' => DB::raw('COALESCE(total_cost, 0)')]);
        DB::table('bom_items')->where('qty_required', 0)->update(['qty_required' => DB::raw('quantity')]);
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('production_items');

        Schema::table('bom_items', function (Blueprint $table): void {
            foreach (['qty_required', 'waste_percent'] as $column) {
                if (Schema::hasColumn('bom_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('materials', function (Blueprint $table): void {
            foreach (['sku', 'minimum_stock', 'cost_price', 'supplier_name'] as $column) {
                if (Schema::hasColumn('materials', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            foreach (['unit', 'cost_price', 'image'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
