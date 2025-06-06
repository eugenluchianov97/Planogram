<?php

namespace Database\Seeders;

use App\Models\Showcase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Showcase::create([
                'name' => "Витрина " . $i+1,
                'description' => "Витрина $i",
                'width' => random_int(500, max: 3000),
                'height' => random_int(500, max: 3000),
                // 'shelf_depth' => random_int(50, 100),
                'comment' => "",
                'shop_id' => 1,
            ]);
        }
    }
}
