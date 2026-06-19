<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Build calmly, sell clearly.');
})->purpose('Display an inspiring quote');
