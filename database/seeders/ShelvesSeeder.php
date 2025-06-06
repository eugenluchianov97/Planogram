<?php

namespace Database\Seeders;

use App\Models\Shelves;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use SebastianBergmann\CodeCoverage\Driver\Selector;

class ShelvesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Shelves::create([
                'position' => $i,
                'showcase_id' => rand(1, 5),
                'distance' => random_int(40, 70),
                'shelf_depth' => random_int(50, 100),
            ]);
        }
    }
}
