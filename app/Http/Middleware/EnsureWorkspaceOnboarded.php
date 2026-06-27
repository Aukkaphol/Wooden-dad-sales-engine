<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceOnboarded
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isSystemAdmin() && ! $user->hasAnyWorkspace()) {
            return redirect()->route('onboarding.workspace.create');
        }

        return $next($request);
    }
}
