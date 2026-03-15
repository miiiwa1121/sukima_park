<?php
/**
 * ============================================================
 * テストユーザーシーダー (TestUserSeeder.php)
 * ============================================================
 * 
 * 開発・テスト用のユーザーデータを登録するSeeder
 * 
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=TestUserSeeder
 * 
 * 【登録内容】
 * - EMAIL: test@example.com
 * - PASSWORD: password123
 * - USERNAME: テストユーザー
 * 
 * 【注意】
 * - 本番環境では実行しないでください
 * - このユーザー（USER_ID=1）で土地やレンタルデータを作成します
 * 
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * テスト用のユーザーデータを登録
     */
    public function run(): void
    {
        // テストユーザーを作成（USER_ID=1として登録される）
        Member::create([
            'EMAIL' => 'test@example.com',           // ログイン用メールアドレス
            'PASSWORD' => Hash::make('password123'), // パスワード（ハッシュ化）
            'TEL' => '090-1234-5678',                // 電話番号
            'BIRTH' => '1990-01-01',                 // 生年月日
            'SHOW_BIRTH' => false,                   // 生年月日の公開設定
            'GENDER' => 0,                           // 性別（0=男性）
            'SHOW_GENDER' => false,                  // 性別の公開設定
            'IDENTITY' => 'test.jpg',                // 本人確認書類
            'USERNAME' => 'テストユーザー',            // ユーザー名
        ]);
        
        $this->command->info('テストユーザー（test@example.com）を登録しました！');
    }
}

