<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('REVIEW_COMMENT_TABLE', function (Blueprint $table) {
            $table->integer('REVIEW_COMMENT_ID')->autoIncrement();
            $table->integer('LAND_REVIEW');
            $table->string('LAND_COMMENT', 512)->nullable();
            $table->integer('USER_REVIEW');
            $table->string('USER_COMMENT', 512)->nullable();
            $table->date('DATE');
            $table->integer('USER_ID');
            $table->integer('LAND_ID');
            $table->integer('RECORD_ID');
            $table->foreign('USER_ID')->references('USER_ID')->on('MEMBER_TABLE');
            $table->foreign('LAND_ID')->references('LAND_ID')->on('LAND_TABLE');
            $table->foreign('RECORD_ID')->references('RECORD_ID')->on('RENTAL_RECORD_TABLE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('REVIEW_COMMENT_TABLE');
    }
};
