<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publishing_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('publishing_queue_item_id')->index();
            $table->uuid('social_account_id')->nullable()->index();
            $table->uuid('created_by')->nullable()->index();
            $table->string('platform')->index();
            $table->string('status')->default('draft')->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->longText('failure_reason')->nullable();
            $table->string('provider_post_id')->nullable()->index();
            $table->json('provider_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('publishing_queue_item_id')->references('id')->on('publishing_queue_items')->cascadeOnDelete();
            $table->foreign('social_account_id')->references('id')->on('social_accounts')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('publishing_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('publishing_job_id')->index();
            $table->uuid('actor_id')->nullable()->index();
            $table->string('level')->default('info')->index();
            $table->string('event')->index();
            $table->longText('message');
            $table->json('context')->nullable();
            $table->timestamps();

            $table->foreign('publishing_job_id')->references('id')->on('publishing_jobs')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publishing_logs');
        Schema::dropIfExists('publishing_jobs');
    }
};
