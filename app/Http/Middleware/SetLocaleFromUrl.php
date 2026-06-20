<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');

        if (! in_array($locale, ['th', 'en'], true)) {
            return redirect('/th');
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        View::share('currentLocale', $locale);
        View::share('alternateLocale', $locale === 'th' ? 'en' : 'th');
        session(['locale' => $locale]);

        return $next($request);
    }
}
