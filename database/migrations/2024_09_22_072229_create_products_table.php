<?php

use App\Models\Category;
use App\Models\Producer;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('Code');
            $table->string('Name');
            $table->text('FullDescription');
            $table->float('BoxWidth');
            $table->float('BoxHeight');
            $table->float('BoxDepth');
            $table->float('Price');
            $table->float('Purchase_price')->nullable();
            $table->float('Margin');
            $table->float('Margin_rate');
            $table->string('Producer')->nullable();
            $table->integer('shop_id');
            $table->foreignIdFor(Category::class)->nullable();
            $table->foreignIdFor(Producer::class)->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
