<?php

use App\Models\Product;
use App\Models\Shelves;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_shelves', function (Blueprint $table) {
            $table->foreignIdFor(Shelves::class);
            $table->foreignIdFor(Product::class);
            $table->integer('deg')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_shelves');
    }
};
