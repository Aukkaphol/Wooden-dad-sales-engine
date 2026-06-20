<?php

use App\Models\CompanySetting;

if (! function_exists('company')) {
    function company(): CompanySetting
    {
        return CompanySetting::current();
    }
}
