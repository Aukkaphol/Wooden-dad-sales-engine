<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('craftsman_production_order');
        Schema::dropIfExists('craftsmen');

        Schema::create('craftsmen', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('craftsman_production_order', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('craftsman_id')->constrained()->cascadeOnDelete();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['craftsman_id', 'production_order_id'], 'craftsman_po_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craftsman_production_order');
        Schema::dropIfExists('craftsmen');
    }
};
