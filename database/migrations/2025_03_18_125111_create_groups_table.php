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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('Key')->unique();
            $table->string('Description');
            $table->string('Parent_Key')->nullable();
            $table->timestamps();

        });

        Schema::table('groups',function (Blueprint $table){
            $table->foreign('Parent_Key')->references('Key')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
