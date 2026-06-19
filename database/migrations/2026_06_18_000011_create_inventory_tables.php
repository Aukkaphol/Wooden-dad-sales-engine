<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit');
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('reserved_stock', 12, 3)->default(0);
            $table->decimal('low_stock_level', 12, 3)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('bom_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 12, 3);
            $table->timestamps();
            $table->unique(['product_id', 'material_id']);
        });

        Schema::create('stock_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('production_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        $this->seedMasterData();
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('bom_items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('materials');
    }

    private function seedMasterData(): void
    {
        $materials = [
            ['name' => 'Pine Board 12 mm', 'unit' => 'sheet', 'low_stock_level' => 5, 'unit_cost' => 420],
            ['name' => 'Pine Board 15 mm', 'unit' => 'sheet', 'low_stock_level' => 5, 'unit_cost' => 520],
            ['name' => 'Pine Board 20 mm', 'unit' => 'sheet', 'low_stock_level' => 5, 'unit_cost' => 680],
            ['name' => 'Pine Board 24 mm', 'unit' => 'sheet', 'low_stock_level' => 5, 'unit_cost' => 780],
            ['name' => 'Wood Screw', 'unit' => 'pcs', 'low_stock_level' => 200, 'unit_cost' => 1.5],
            ['name' => 'Drawer Slide', 'unit' => 'pcs', 'low_stock_level' => 20, 'unit_cost' => 150],
            ['name' => 'Handle', 'unit' => 'pcs', 'low_stock_level' => 20, 'unit_cost' => 80],
            ['name' => 'Wood Stain', 'unit' => 'litre', 'low_stock_level' => 5, 'unit_cost' => 320],
            ['name' => 'Lacquer', 'unit' => 'litre', 'low_stock_level' => 5, 'unit_cost' => 360],
        ];

        foreach ($materials as $material) {
            DB::table('materials')->insert([
                ...$material,
                'current_stock' => 0,
                'reserved_stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $productId = DB::table('products')->insertGetId([
            'name' => 'Bed 6 ft',
            'description' => 'Standard Wooden Dad Design 6 ft bed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([
            'Pine Board 20 mm' => 2.3,
            'Wood Screw' => 48,
            'Wood Stain' => 0.8,
        ] as $materialName => $quantity) {
            DB::table('bom_items')->insert([
                'product_id' => $productId,
                'material_id' => DB::table('materials')->where('name', $materialName)->value('id'),
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
