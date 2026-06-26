<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publishing_queue_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('generated_content_id')->index();
            $table->uuid('created_by')->nullable()->index();
            $table->string('platform')->index();
            $table->string('status')->default('waiting')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->longText('failure_reason')->nullable();
            $table->unsignedInteger('priority')->default(100)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('publishing_queue_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('publishing_queue_item_id')->index();
            $table->uuid('actor_id')->nullable()->index();
            $table->string('event')->index();
            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();
            $table->longText('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('publishing_queue_item_id')->references('id')->on('publishing_queue_items')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publishing_queue_histories');
        Schema::dropIfExists('publishing_queue_items');
    }
};
