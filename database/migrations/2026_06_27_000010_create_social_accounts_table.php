<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('brand_id')->nullable()->index();
            $table->uuid('connected_by')->nullable()->index();
            $table->string('platform')->index();
            $table->string('provider_account_id')->nullable()->index();
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('status')->default('draft')->index();
            $table->json('scopes')->nullable();
            $table->json('oauth_payload')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
            $table->foreign('connected_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
