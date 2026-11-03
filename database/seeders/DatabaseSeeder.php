<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['username' => env('ADMIN_USERNAME', 'admin')],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'email' => env('ADMIN_EMAIL', 'admin@shreemahakal.test'),
                'password' => env('ADMIN_PASSWORD', 'password'),
            ],
        );

        $this->call(DemoDataSeeder::class);
    }
}
