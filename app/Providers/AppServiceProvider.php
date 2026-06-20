<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $helpers = app_path('helpers.php');

        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    public function boot(): void
    {
        //
    }
}
