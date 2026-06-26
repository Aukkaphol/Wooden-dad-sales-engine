<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->index();
            $table->uuid('uploaded_by')->index();
            $table->string('name');
            $table->string('type')->index();
            $table->string('mime_type');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->json('metadata')->nullable();
            $table->json('tags')->nullable();
            $table->string('category')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['workspace_id', 'brand_id', 'type']);
            $table->index(['workspace_id', 'status']);
            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
