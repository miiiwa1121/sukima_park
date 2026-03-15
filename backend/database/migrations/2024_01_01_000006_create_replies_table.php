<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('REPLY_TABLE', function (Blueprint $table) {
            $table->integer('REPLY_ID')->autoIncrement();
            $table->integer('CONTACT_ID');
            $table->integer('USER_ID');
            $table->string('MESSAGE', 1024);
            $table->date('DATE');
            $table->foreign('CONTACT_ID')->references('CONTACT_ID')->on('CONTACT_TABLE');
            $table->foreign('USER_ID')->references('USER_ID')->on('MEMBER_TABLE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('REPLY_TABLE');
    }
};
