<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly StatefulGuard $guard,
        private readonly UserRepositoryInterface $users,
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function register(array $attributes, Request $request): User
    {
        return DB::transaction(function () use ($attributes, $request): User {
            $user = $this->users->create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
                'timezone' => $attributes['timezone'] ?? 'UTC',
                'locale' => $attributes['locale'] ?? 'en',
                'status' => 'active',
            ]);

            event(new Registered($user));

            $this->guard->login($user);
            $request->session()->regenerate();

            $this->activityLog->queue(
                event: 'auth.registered',
                description: 'User registered.',
                subject: $user,
                request: $request,
                userId: $user->getKey(),
            );

            return $user;
        });
    }

    public function login(Request $request, bool $remember = false): User
    {
        if (! $this->guard->attempt($request->only('email', 'password') + ['status' => 'active'], $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $this->guard->user();
        $this->users->markLastLogin($user);

        $this->activityLog->queue(
            event: 'auth.login',
            description: 'User logged in.',
            subject: $user,
            request: $request,
            userId: $user->getKey(),
        );

        return $user;
    }

    public function logout(Request $request): void
    {
        /** @var User|null $user */
        $user = $this->guard->user();

        if ($user) {
            $this->activityLog->queue(
                event: 'auth.logout',
                description: 'User logged out.',
                subject: $user,
                request: $request,
                userId: $user->getKey(),
            );
        }

        $this->guard->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
