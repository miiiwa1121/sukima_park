<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CHAT_TABLE', function (Blueprint $table) {
            $table->integer('CHAT_ID')->autoIncrement();
            $table->integer('USER_ID_FROM');
            $table->integer('USER_ID_TO');
            $table->string('MESSAGE', 512);
            $table->string('IMAGE', 2048)->nullable();
            $table->date('YEAR');
            $table->date('DATE');
            $table->time('TIME');
            $table->foreign('USER_ID_FROM')->references('USER_ID')->on('MEMBER_TABLE');
            $table->foreign('USER_ID_TO')->references('USER_ID')->on('MEMBER_TABLE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CHAT_TABLE');
    }
};
