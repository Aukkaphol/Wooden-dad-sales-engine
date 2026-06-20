<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@woodendad.local'],
            [
                'name' => 'Wooden Dad Admin',
                'password' => Hash::make('password'),
            ]
        );

        CompanySetting::current();

        $this->call(WebsiteContentSeeder::class);
    }
}
