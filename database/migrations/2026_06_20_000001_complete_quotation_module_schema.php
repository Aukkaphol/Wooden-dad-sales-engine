<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            if (! Schema::hasColumn('quotations', 'quotation_no')) {
                $table->string('quotation_no')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('quotations', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('lead_id');
            }

            if (! Schema::hasColumn('quotations', 'phone')) {
                $table->string('phone')->nullable()->after('customer_name');
            }

            if (! Schema::hasColumn('quotations', 'province')) {
                $table->string('province')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('quotations', 'project_name')) {
                $table->string('project_name')->nullable()->after('province');
            }

            if (! Schema::hasColumn('quotations', 'discount')) {
                $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            }

            if (! Schema::hasColumn('quotations', 'shipping_cost')) {
                $table->decimal('shipping_cost', 12, 2)->default(0)->after('discount');
            }

            if (! Schema::hasColumn('quotations', 'deposit_amount')) {
                $table->decimal('deposit_amount', 12, 2)->default(0)->after('shipping_cost');
            }

            if (! Schema::hasColumn('quotations', 'grand_total')) {
                $table->decimal('grand_total', 12, 2)->default(0)->after('deposit_amount');
            }

            if (! Schema::hasColumn('quotations', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('grand_total');
            }

            if (! Schema::hasColumn('quotations', 'remark')) {
                $table->text('remark')->nullable()->after('valid_until');
            }

            if (! Schema::hasColumn('quotations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('remark');
            }
        });

        Schema::table('quotation_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('quotation_items', 'item_name')) {
                $table->string('item_name')->nullable()->after('quotation_id');
            }

            if (! Schema::hasColumn('quotation_items', 'description')) {
                $table->text('description')->nullable()->after('item_name');
            }

            if (! Schema::hasColumn('quotation_items', 'qty')) {
                $table->decimal('qty', 10, 2)->nullable()->after('description');
            }

            if (! Schema::hasColumn('quotation_items', 'unit')) {
                $table->string('unit')->default('ชุด')->after('qty');
            }

            if (! Schema::hasColumn('quotation_items', 'total_price')) {
                $table->decimal('total_price', 12, 2)->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table): void {
            foreach (['item_name', 'description', 'qty', 'unit', 'total_price'] as $column) {
                if (Schema::hasColumn('quotation_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('quotations', function (Blueprint $table): void {
            foreach ([
                'quotation_no',
                'customer_name',
                'phone',
                'province',
                'project_name',
                'discount',
                'shipping_cost',
                'deposit_amount',
                'grand_total',
                'valid_until',
                'remark',
                'approved_at',
            ] as $column) {
                if (Schema::hasColumn('quotations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
