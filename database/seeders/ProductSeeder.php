<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Producer;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create(['name' => 'category1']);
        $producer1 = Producer::create(['name' => 'Producer1']);

        Product::create([
            "Code" => "ССAAA.5785",
            "Name" => "Gel de dus Palmolive KIDS pompa 750ml",
            "FullDescription" => "Gel de dus Palmolive KIDS pompa 750ml",
            "BoxHeight" => 30.0,
            "BoxWidth" => 12.0,
            "BoxDepth" => 25.0,
            "Price" => 95.2,
            "shop_id" => 31,


            'Purchase_price' => 33.4,
            'Margin' => 33.2,
            'Margin_rate' => 3.22,
            'Producer' => "test producer1",
            'category_id' => $category->id,
            'producer_id' => $producer1->id,
        ]);

        $category = Category::create(['name' => 'category2']);
        $producer2 = Producer::create(['name' => 'Producer2']);

        Product::create([
            "Code" => "T000007177",
            "Name" => "FINO Lavete perforate 3 buc.",
            "FullDescription" => "FINO Lavete perforate 3 buc.",
            "BoxHeight" => 20.0,
            "BoxWidth" => 40.0,
            "BoxDepth" => 20.0,
            "Price" => 10,
            "shop_id" => 31,

            'Purchase_price' => 33.4,
            'Margin' => 33.2,
            'Margin_rate' => 3.22,
            'Producer' => "test producer2",
            'category_id' => $category->id,
            'producer_id' => $producer2->id,
        ]);

        $category = Category::create(['name' => 'category3']);
        $producer3 = Producer::create(['name' => 'Producer3']);

        Product::create([
            "Code" => "T000007182",
            "Name" => "FINO Folie plastic-film (sleeve) 30 M",
            "FullDescription" => "FINO Folie plastic-film (sleeve) 30 M",
            "BoxHeight" => 20.0,
            "BoxWidth" => 30.0,
            "BoxDepth" => 45.0,
            "Price" => 10,
            "shop_id" => 31,

            'Purchase_price' => 33.4,
            'Margin' => 33.2,
            'Margin_rate' => 3.22,
            'Producer' => "test producer3",
            'category_id' => $category->id,
            'producer_id' => $producer3->id,
        ]);
    }
}
