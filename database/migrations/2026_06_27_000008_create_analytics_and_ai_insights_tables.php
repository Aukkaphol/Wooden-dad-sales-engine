<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('generated_content_id')->index();
            $table->uuid('publishing_queue_item_id')->nullable()->index();
            $table->uuid('created_by')->nullable()->index();
            $table->string('platform')->index();
            $table->timestamp('posted_at')->nullable()->index();
            $table->timestamp('captured_at')->index();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('reach')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('comments')->default(0);
            $table->unsignedBigInteger('shares')->default(0);
            $table->unsignedBigInteger('saves')->default(0);
            $table->unsignedBigInteger('follows_gained')->default(0);
            $table->unsignedBigInteger('link_clicks')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->decimal('engagement_rate', 8, 4)->default(0);
            $table->decimal('estimated_revenue', 12, 2)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->decimal('roas', 10, 4)->default(0);
            $table->longText('notes')->nullable();
            $table->json('audience_breakdown')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedTinyInteger('score')->default(0);
            $table->longText('score_reason')->nullable();
            $table->longText('recommendation')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->cascadeOnDelete();
            $table->foreign('publishing_queue_item_id')->references('id')->on('publishing_queue_items')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('ai_insights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('generated_content_id')->index();
            $table->uuid('analytics_record_id')->nullable()->index();
            $table->uuid('created_by')->nullable()->index();
            $table->string('insight_type')->index();
            $table->string('title');
            $table->longText('summary');
            $table->longText('recommendation')->nullable();
            $table->string('priority')->default('medium')->index();
            $table->string('status')->default('new')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->cascadeOnDelete();
            $table->foreign('analytics_record_id')->references('id')->on('analytics_records')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
        Schema::dropIfExists('analytics_records');
    }
};
