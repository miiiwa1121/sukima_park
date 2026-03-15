<?php

/**
 * ============================================================
 * マイグレーションファイル: remember_tokenカラム追加
 * ============================================================
 * 
 * 【このマイグレーションの役割】
 * MEMBER_TABLEにremember_tokenカラムを追加します。
 * 
 * 【remember_tokenとは】
 * 「ログイン状態を保持する」（Remember Me）機能で使用するトークンです。
 * ログイン時にチェックボックスをオンにすると、このカラムにランダムな
 * トークンが保存され、ブラウザを閉じても自動ログインが可能になります。
 * 
 * 【実行コマンド】
 * マイグレーション実行: php artisan migrate
 * ロールバック: php artisan migrate:rollback
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
     * 【処理内容】
     * MEMBER_TABLEにremember_tokenカラムを追加します。
     * - rememberToken(): varchar(100)のカラムを作成
     * - nullable(): NULL値を許可（ログイン状態を保持しない場合はNULL）
     * 
     * 【なぜnullable?】
     * すべてのユーザーがRemember Me機能を使うわけではないため、
     * NULL値を許可する必要があります。
     */
    public function up(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            // remember_tokenカラムを追加（varchar(100), NULL許可）
            $table->rememberToken()->nullable();
        });
    }

    /**
     * マイグレーションをロールバック（元に戻す）
     * 
     * 【処理内容】
     * MEMBER_TABLEからremember_tokenカラムを削除します。
     */
    public function down(): void
    {
        Schema::table('MEMBER_TABLE', function (Blueprint $table) {
            // remember_tokenカラムを削除
            $table->dropRememberToken();
        });
    }
};
