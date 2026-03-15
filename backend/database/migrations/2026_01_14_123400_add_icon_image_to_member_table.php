<?php

/**
 * MEMBER_TABLEにICON_IMAGEカラムを追加
 * プロフィールアイコン画像のパスを保存するために使用
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            if (!Schema::hasColumn('MEMBER_TABLE', 'ICON_IMAGE')) {
                $table->string('ICON_IMAGE')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            $table->dropColumn('ICON_IMAGE');
        });
    }
};
