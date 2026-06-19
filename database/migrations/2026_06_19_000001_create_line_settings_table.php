<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_settings', function (Blueprint $table): void {
            $table->id();
            $table->text('channel_access_token')->nullable();
            $table->string('admin_recipient_id')->nullable();
            $table->string('production_group_id')->nullable();
            $table->string('delivery_group_id')->nullable();
            $table->boolean('notifications_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_settings');
    }
};
