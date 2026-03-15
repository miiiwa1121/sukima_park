<?php
/**
 * ============================================================
 * データベースシーダー (DatabaseSeeder.php)
 * ============================================================
 * 
 * アプリケーション全体のテストデータを一括登録するメインシーダー
 * 
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed
 * 
 * 【実行内容】
 * 以下のシーダーを順番に実行します：
 * 1. TestUserSeeder     - テストユーザー（1件）
 * 2. TestLandSeeder     - テスト用土地データ（5件）
 * 3. TestRentalSeeder   - レンタル記録（3件）+ レビュー（2件）
 * 
 * 【注意】
 * - 開発環境でのみ使用してください
 * - 本番環境では実行しないでください
 * - データベースをリセットする場合: php artisan migrate:fresh --seed
 * 
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * アプリケーション全体のテストデータを登録
     * 
     * 【実行順序の重要性】
     * - TestUserSeeder を最初に実行（USER_ID=1を作成）
     * - TestLandSeeder でそのユーザーの土地を作成
     * - TestRentalSeeder でレンタル記録とレビューを作成
     * 
     * この順序を変えると外部キー制約エラーが発生します
     */
    public function run(): void
    {
        // 各シーダーを順番に実行
        $this->call([
            TestUserSeeder::class,    // ① ユーザー作成
            TestLandSeeder::class,    // ② 土地データ作成（USER_ID=1の土地）
            TestRentalSeeder::class,  // ③ レンタル記録とレビュー作成
        ]);
    }
}
