<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->string('supplier_code')->unique();
            $table->string('supplier_name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('line_id')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('purchase_requisitions', function (Blueprint $table): void {
            $table->id();
            $table->string('pr_number')->unique();
            $table->date('request_date');
            $table->string('requested_by');
            $table->text('reason')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('production_order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_requisition_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 12, 3);
            $table->string('unit');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('purchase_requisition_id')->nullable()->constrained()->nullOnDelete();
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 12, 3);
            $table->string('unit');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('received_quantity', 12, 3)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->restrictOnDelete();
            $table->date('receive_date');
            $table->decimal('ordered_quantity', 12, 3);
            $table->decimal('received_quantity', 12, 3);
            $table->decimal('remaining_quantity', 12, 3)->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_requisition_items');
        Schema::dropIfExists('purchase_requisitions');
        Schema::dropIfExists('suppliers');
    }
};
