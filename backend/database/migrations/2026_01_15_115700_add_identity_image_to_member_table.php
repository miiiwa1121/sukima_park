<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            $table->string('IDENTITY_IMAGE', 1024)->nullable()->after('IDENTITY');
        });
    }

    public function down(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            $table->dropColumn('IDENTITY_IMAGE');
        });
    }
};
