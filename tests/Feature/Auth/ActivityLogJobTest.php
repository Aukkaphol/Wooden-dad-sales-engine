<?php

namespace Tests\Feature\Auth;

use App\DTOs\ActivityLogData;
use App\Jobs\LogActivityJob;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_log_job_persists_audit_event(): void
    {
        $user = User::factory()->create();

        (new LogActivityJob(new ActivityLogData(
            userId: $user->getKey(),
            event: 'auth.login',
            description: 'User logged in.',
            properties: ['source' => 'test'],
            ipAddress: '127.0.0.1',
            userAgent: 'Feature Test',
            subjectType: $user->getMorphClass(),
            subjectId: $user->getKey(),
        )))->handle(app(\App\Repositories\Contracts\ActivityLogRepositoryInterface::class));

        $this->assertDatabaseHas(ActivityLog::class, [
            'user_id' => $user->getKey(),
            'event' => 'auth.login',
            'subject_type' => $user->getMorphClass(),
            'subject_id' => $user->getKey(),
        ]);
    }
}
