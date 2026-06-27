<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->string('facebook_user_id')->nullable()->index();
            $table->string('facebook_user_name')->nullable();
            $table->text('facebook_user_avatar')->nullable();
            $table->string('page_id')->index();
            $table->string('page_name');
            $table->text('page_avatar')->nullable();
            $table->string('page_category')->nullable();
            $table->unsignedBigInteger('page_followers_count')->nullable();
            $table->unsignedBigInteger('page_likes_count')->nullable();
            $table->string('page_verification_status')->nullable();
            $table->text('page_access_token');
            $table->timestamp('token_expires_at')->nullable();
            $table->json('permissions')->nullable();
            $table->string('status')->default('active')->index();
            $table->string('connection_status')->default('active')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_connections');
    }
};
