<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::create([
            'name' => 'Торговая Точка 1',
            'address' => 'Адрес Торговая Точка 1',
            'c_id' => 31
        ]);

        Shop::create([
            'name' => 'Торговая Точка 2',
            'address' => 'Адрес Торговая Точка 2',
            'c_id' => 32
        ]);
    }
}
