<?php

/**
 * MEMBER_TABLEに不足しているすべてのカラムを追加
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            // 各カラムが存在しない場合のみ追加
            if (!Schema::hasColumn('MEMBER_TABLE', 'ACCOUNT_STATUS')) {
                $table->integer('ACCOUNT_STATUS')->default(0)->after('ICON_IMAGE');
            }
            if (!Schema::hasColumn('MEMBER_TABLE', 'SHOW_BIRTH')) {
                $table->boolean('SHOW_BIRTH')->default(false);
            }
            if (!Schema::hasColumn('MEMBER_TABLE', 'SHOW_GENDER')) {
                $table->boolean('SHOW_GENDER')->default(false);
            }
            if (!Schema::hasColumn('MEMBER_TABLE', 'IDENTITY')) {
                $table->boolean('IDENTITY')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            $table->dropColumn(['ACCOUNT_STATUS', 'SHOW_BIRTH', 'SHOW_GENDER', 'IDENTITY']);
        });
    }
};
