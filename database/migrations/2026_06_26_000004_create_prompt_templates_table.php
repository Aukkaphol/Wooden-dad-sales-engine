<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('created_by')->index();
            $table->string('title');
            $table->string('slug');
            $table->string('category')->index();
            $table->string('platform')->index();
            $table->longText('prompt_template');
            $table->json('variables')->nullable();
            $table->longText('example_output')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('draft')->index();
            $table->json('tags')->nullable();
            $table->boolean('favorite')->default(false)->index();
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->decimal('success_rate', 5, 2)->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->string('recommended_model')->nullable()->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['workspace_id', 'brand_id', 'slug']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('prompt_template_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('prompt_template_id')->index();
            $table->uuid('created_by')->nullable()->index();
            $table->unsignedInteger('version');
            $table->string('title');
            $table->string('category');
            $table->string('platform');
            $table->longText('prompt_template');
            $table->json('variables')->nullable();
            $table->longText('example_output')->nullable();
            $table->string('status');
            $table->json('tags')->nullable();
            $table->string('recommended_model')->nullable();
            $table->timestamps();

            $table->unique(['prompt_template_id', 'version']);
            $table->foreign('prompt_template_id')->references('id')->on('prompt_templates')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompt_template_versions');
        Schema::dropIfExists('prompt_templates');
    }
};
