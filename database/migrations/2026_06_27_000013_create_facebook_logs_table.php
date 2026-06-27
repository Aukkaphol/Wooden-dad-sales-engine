<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id')->index();
            $table->uuid('facebook_connection_id')->nullable()->index();
            $table->string('action')->index();
            $table->string('status')->index();
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->foreign('facebook_connection_id')->references('id')->on('facebook_connections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_logs');
    }
};
