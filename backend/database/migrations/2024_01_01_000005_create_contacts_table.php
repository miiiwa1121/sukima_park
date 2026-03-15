<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CONTACT_TABLE', function (Blueprint $table) {
            $table->integer('CONTACT_ID')->autoIncrement();
            $table->string('TITLE', 128);
            $table->string('MESSAGE', 1024);
            $table->integer('USER_ID');
            $table->date('DATE');
            $table->integer('STATUS')->default(0);
            $table->foreign('USER_ID')->references('USER_ID')->on('MEMBER_TABLE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CONTACT_TABLE');
    }
};
