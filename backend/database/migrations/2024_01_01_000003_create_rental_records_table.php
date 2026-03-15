<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('RENTAL_RECORD_TABLE', function (Blueprint $table) {
            $table->integer('RECORD_ID')->autoIncrement();
            $table->integer('PRICE');
            $table->integer('PRICE_UNIT');
            $table->date('RENTAL_START_DATE');
            $table->date('RENTAL_END_DATE');
            $table->time('RENTAL_START_TIME');
            $table->time('RENTAL_END_TIME');
            $table->integer('LAND_ID');
            $table->integer('USER_ID');
            $table->foreign('LAND_ID')->references('LAND_ID')->on('LAND_TABLE');
            $table->foreign('USER_ID')->references('USER_ID')->on('MEMBER_TABLE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('RENTAL_RECORD_TABLE');
    }
};
