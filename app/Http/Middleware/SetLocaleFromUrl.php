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
        $alternateLocale = $locale === 'th' ? 'en' : 'th';
        $segments = $request->segments();

        if (isset($segments[0]) && in_array($segments[0], ['th', 'en'], true)) {
            $segments[0] = $alternateLocale;
        } else {
            array_unshift($segments, $alternateLocale);
        }

        $alternateLocaleUrl = url(implode('/', $segments)).($request->getQueryString() ? '?'.$request->getQueryString() : '');

        View::share('currentLocale', $locale);
        View::share('alternateLocale', $alternateLocale);
        View::share('alternateLocaleUrl', $alternateLocaleUrl);
        session(['locale' => $locale]);

        return $next($request);
    }
}
