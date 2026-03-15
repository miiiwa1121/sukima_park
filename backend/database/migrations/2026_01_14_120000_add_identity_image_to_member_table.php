<?php

/**
 * ============================================================
 * マイグレーション: IDENTITY_IMAGEカラム追加
 * ============================================================
 * 
 * 【このマイグレーションの役割】
 * MEMBER_TABLEにIDENTITY_IMAGEカラムを追加します。
 * 本人確認書類の画像ファイルパスを保存するために使用します。
 * 
 * 【実行コマンド】
 * マイグレーション実行: docker exec -it sukima_park-laravel.test-1 php artisan migrate
 * ロールバック: docker exec -it sukima_park-laravel.test-1 php artisan migrate:rollback
 * 
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーションを実行
     * 
     * MEMBER_TABLEにIDENTITY_IMAGEカラムを追加します。
     * - string: varchar(255)のカラムを作成
     * - nullable(): NULL値を許可（本人確認書類がない場合はNULL）
     */
    public function up(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            // IDENTITY_IMAGEカラムを追加（varchar(255), NULL許可）
            // カラムが存在しない場合のみ追加
            if (!Schema::hasColumn('MEMBER_TABLE', 'IDENTITY_IMAGE')) {
                $table->string('IDENTITY_IMAGE')->nullable();
            }
        });
    }

    /**
     * マイグレーションをロールバック（元に戻す）
     * 
     * MEMBER_TABLEからIDENTITY_IMAGEカラムを削除します。
     */
    public function down(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            $table->dropColumn('IDENTITY_IMAGE');
        });
    }
};
