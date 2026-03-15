<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('MEMBER_TABLE', function (Blueprint $table) {
            $table->integer('USER_ID')->autoIncrement();
            $table->string('EMAIL', 1024);
            $table->string('PASSWORD', 64);
            $table->string('TEL', 64);
            $table->date('BIRTH');
            $table->boolean('SHOW_BIRTH')->default(false);
            $table->integer('GENDER');
            $table->boolean('SHOW_GENDER')->default(false);
            $table->string('IDENTITY', 1024);
            $table->string('USERNAME', 128);
            $table->string('SELF_INTRODUCTION', 512)->nullable();
            $table->string('ICON_IMAGE', 1024)->default('default_icon.png');
            $table->integer('ACCOUNT_STATUS')->default(0); // 0:ユーザ 1:凍結 2:管理者
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('MEMBER_TABLE');
    }
};
