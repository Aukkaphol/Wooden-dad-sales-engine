<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_integration_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->unique();
            $table->string('app_id');
            $table->text('app_secret');
            $table->string('redirect_uri');
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_integration_settings');
    }
};
