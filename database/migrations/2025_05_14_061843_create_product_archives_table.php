<?php

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
        Schema::create('product_archives', function (Blueprint $table) {
            $table->id();

            $table->integer('shop');
            $table->string('group');
            $table->string('code');
            $table->string('year');
            $table->string('month');


            $table->string('PretAchizitie')->nullable();
            $table->string('PretVanzare')->nullable();
            $table->string('Profit')->nullable();
            $table->string('Adaos')->nullable();
            $table->string('Marja')->nullable();
            $table->string('Discount')->nullable();
            $table->string('DiscountLei')->nullable();
            $table->string('TotalMarjaDoscount')->nullable();
            $table->string('ProfitInclDiscountLei')->nullable();
            $table->string('PretPromo')->nullable();
            $table->string('PercentDifPretPromo')->nullable();
            $table->string('RetroToMDL')->nullable();
            $table->string('QuantityPromo')->nullable();
            $table->string('CantitateVanzare')->nullable();
            $table->string('SumaVanzare')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_archives');
    }
};
