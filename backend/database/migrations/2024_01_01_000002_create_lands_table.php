<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('LAND_TABLE', function (Blueprint $table) {
            $table->integer('LAND_ID')->autoIncrement();
            $table->string('NAME', 128); // 土地名: 40文字以下
            $table->integer('PEREFECTURES');
            $table->string('CITY', 256);
            $table->string('STREET_ADDRESS', 256);
            $table->decimal('AREA', 5, 2);
            $table->string('IMAGE', 2048)->nullable();
            $table->string('TITLE_DEED', 2048);
            $table->string('DESCRIPTION', 4096)->nullable();
            $table->date('RENTAL_START_DATE')->nullable();
            $table->date('RENTAL_END_DATE')->nullable();
            $table->time('RENTAL_START_TIME')->nullable();
            $table->time('RENTAL_END_TIME')->nullable();
            $table->integer('PRICE');
            $table->integer('PRICE_UNIT'); // 0:日 1:時間 2:15分
            $table->integer('USER_ID');
            $table->boolean('STATUS')->default(false); // 0:非公開 1:公開中
            $table->foreign('USER_ID')->references('USER_ID')->on('MEMBER_TABLE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('LAND_TABLE');
    }
};
