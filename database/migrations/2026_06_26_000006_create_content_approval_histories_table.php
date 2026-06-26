<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_contents', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('status');
            $table->timestamp('published_at')->nullable()->after('scheduled_at');
            $table->longText('reviewer_notes')->nullable()->after('published_at');
        });

        Schema::create('content_approval_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('generated_content_id')->index();
            $table->uuid('reviewer_id')->nullable()->index();
            $table->string('decision')->index();
            $table->longText('comment')->nullable();
            $table->string('previous_status')->index();
            $table->string('new_status')->index();
            $table->timestamp('decided_at')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('generated_content_id')->references('id')->on('generated_contents')->cascadeOnDelete();
            $table->foreign('reviewer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_approval_histories');

        Schema::table('generated_contents', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'published_at', 'reviewer_notes']);
        });
    }
};
