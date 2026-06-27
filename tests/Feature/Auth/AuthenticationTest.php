<?php

namespace Tests\Feature\Auth;

use App\Jobs\LogActivityJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_uuid_user_and_queues_activity_log(): void
    {
        Queue::fake();

        $response = $this->post('/register', [
            'name' => 'Jarvis Admin',
            'email' => 'admin@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('onboarding.workspace.create'));

        $user = User::query()->where('email', 'admin@example.test')->firstOrFail();

        $this->assertTrue(Str::isUuid($user->getKey()));
        $this->assertAuthenticatedAs($user);

        Queue::assertPushed(LogActivityJob::class);
    }

    public function test_active_user_can_login_and_logout(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'owner@example.test',
            'password' => 'Password123!',
        ]);

        $this->post('/login', [
            'email' => 'owner@example.test',
            'password' => 'Password123!',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->last_login_at);

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
        Queue::assertPushed(LogActivityJob::class, 2);
    }

    public function test_inactive_user_cannot_login(): void
    {
        Queue::fake();

        User::factory()->create([
            'email' => 'disabled@example.test',
            'password' => 'Password123!',
            'status' => 'disabled',
        ]);

        $this->from('/login')->post('/login', [
            'email' => 'disabled@example.test',
            'password' => 'Password123!',
        ])->assertRedirect('/login');

        $this->assertGuest();
        Queue::assertNothingPushed();
    }
}
