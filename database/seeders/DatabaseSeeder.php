<?php

namespace Database\Seeders;

use App\Models\User;
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

        $this->call(WebsiteContentSeeder::class);
    }
}
