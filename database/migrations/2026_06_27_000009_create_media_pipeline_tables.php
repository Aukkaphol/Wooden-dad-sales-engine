<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_pipeline_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('created_by')->nullable()->index();
            $table->json('asset_ids')->nullable();
            $table->uuid('prompt_template_id')->nullable()->index();
            $table->unsignedInteger('prompt_version')->default(1);
            $table->uuid('generated_content_id')->nullable()->index();
            $table->uuid('publishing_queue_item_id')->nullable()->index();
            $table->uuid('analytics_record_id')->nullable()->index();
            $table->uuid('ai_insight_id')->nullable()->index();
            $table->string('current_stage')->index();
            $table->string('status')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('prompt_template_id')->references('id')->on('prompt_templates')->nullOnDelete();
            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->nullOnDelete();
            $table->foreign('publishing_queue_item_id')->references('id')->on('publishing_queue_items')->nullOnDelete();
            $table->foreign('analytics_record_id')->references('id')->on('analytics_records')->nullOnDelete();
            $table->foreign('ai_insight_id')->references('id')->on('ai_insights')->nullOnDelete();
        });

        Schema::create('media_pipeline_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('media_pipeline_run_id')->index();
            $table->uuid('actor_id')->nullable()->index();
            $table->string('stage')->index();
            $table->string('event')->index();
            $table->string('description');
            $table->nullableUuidMorphs('subject');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('media_pipeline_run_id')->references('id')->on('media_pipeline_runs')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_pipeline_histories');
        Schema::dropIfExists('media_pipeline_runs');
    }
};
