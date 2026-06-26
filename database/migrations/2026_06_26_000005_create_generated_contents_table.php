<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('prompt_template_id')->index();
            $table->uuid('created_by')->index();
            $table->string('title');
            $table->string('platform')->index();
            $table->string('content_type')->index();
            $table->longText('prompt_snapshot');
            $table->json('variables')->nullable();
            $table->longText('generated_content');
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('version')->default(1);
            $table->json('tags')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('prompt_template_id')->references('id')->on('prompt_templates')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::create('generated_content_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('generated_content_id');
            $table->uuid('asset_id');
            $table->timestamps();

            $table->unique(['generated_content_id', 'asset_id']);
            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
        });

        Schema::create('generated_content_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('generated_content_id')->index();
            $table->uuid('created_by')->nullable()->index();
            $table->unsignedInteger('version');
            $table->string('title');
            $table->string('platform');
            $table->string('content_type');
            $table->longText('prompt_snapshot');
            $table->json('variables')->nullable();
            $table->longText('generated_content');
            $table->string('status');
            $table->json('tags')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->unique(['generated_content_id', 'version']);
            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_content_versions');
        Schema::dropIfExists('generated_content_assets');
        Schema::dropIfExists('generated_contents');
    }
};
