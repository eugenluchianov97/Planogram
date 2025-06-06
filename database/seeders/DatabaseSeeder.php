<?php

namespace Database\Seeders;

use App\Models\Showcase;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'User',
            'login' => 'user',
            'password' => bcrypt('password'),
            'role' => 'user',
            'avatar' => '',
        ]);

        User::create([
            'name' => 'Manager',
            'login' => 'manager',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'avatar' => '',
        ]);

        User::create([
            'name' => 'Admin',
            'login' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'avatar' => '',
            'telegram_chat_id' => '6693753226'
        ]);

        $this->call([
            ShopSeeder::class,
            ShowcaseSeeder::class,
            ShelvesSeeder::class,
            ProductSeeder::class
        ]);
    }
}
