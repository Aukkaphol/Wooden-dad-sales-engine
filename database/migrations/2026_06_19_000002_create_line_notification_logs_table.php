<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('event');
            $table->string('channel');
            $table->string('recipient_id')->nullable();
            $table->string('status');
            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->text('message');
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['event', 'channel']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_notification_logs');
    }
};
