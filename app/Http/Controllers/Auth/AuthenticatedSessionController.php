<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function store(LoginRequest $request, AuthService $authService): RedirectResponse
    {
        $authService->login($request, $request->boolean('remember'));

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request, AuthService $authService): RedirectResponse
    {
        $authService->logout($request);

        return redirect()->route('login');
    }
}
