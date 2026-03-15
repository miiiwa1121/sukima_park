<?php
/**
 * ============================================================
 * テストレンタルシーダー (TestRentalSeeder.php)
 * ============================================================
 * 
 * 開発・テスト用のレンタル記録とレビューデータを登録するSeeder
 * 
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=TestRentalSeeder
 * 
 * 【前提条件】
 * - TestUserSeederを実行済み（USER_ID=1のテストユーザーが存在）
 * - TestLandSeederを実行済み（土地データが存在）
 * 
 * 【注意】
 * - 本番環境では実行しないでください
 * - USER_ID=1のテストユーザーがレンタルしたデータが登録されます
 * 
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\RentalRecord;
use App\Models\ReviewComment;
use Illuminate\Database\Seeder;

class TestRentalSeeder extends Seeder
{
    /**
     * テスト用のレンタル記録とレビューデータを登録
     */
    public function run(): void
    {
        // テスト用レンタル記録（3件）
        $rentals = [
            [
                'PRICE' => 20000,
                'PRICE_UNIT' => 0, // 日あたり
                'RENTAL_START_DATE' => now()->addDays(2)->format('Y-m-d'),
                'RENTAL_END_DATE' => now()->addDays(9)->format('Y-m-d'),
                'RENTAL_START_TIME' => '08:00:00',
                'RENTAL_END_TIME' => '20:00:00',
                'LAND_ID' => 1, // 都市型ポップアップスペース
                'USER_ID' => 1, // テストユーザー
            ],
            [
                'PRICE' => 15000,
                'PRICE_UNIT' => 0, // 日あたり
                'RENTAL_START_DATE' => now()->subDays(7)->format('Y-m-d'),
                'RENTAL_END_DATE' => now()->subDays(1)->format('Y-m-d'),
                'RENTAL_START_TIME' => '10:00:00',
                'RENTAL_END_TIME' => '18:00:00',
                'LAND_ID' => 2, // 里山キャンプベース
                'USER_ID' => 1,
            ],
            [
                'PRICE' => 5000,
                'PRICE_UNIT' => 0, // 日あたり
                'RENTAL_START_DATE' => now()->subDays(30)->format('Y-m-d'),
                'RENTAL_END_DATE' => now()->subDays(25)->format('Y-m-d'),
                'RENTAL_START_TIME' => '09:00:00',
                'RENTAL_END_TIME' => '17:00:00',
                'LAND_ID' => 3, // 街角コミュニティガーデン
                'USER_ID' => 1,
            ],
        ];

        foreach ($rentals as $rentalData) {
            RentalRecord::create($rentalData);
        }

        // レビュー記録（取引完了したレンタルへのレビュー）
        // RECORD_ID=2のレンタル（7日前～1日前）にレビューを付与
        ReviewComment::create([
            'LAND_REVIEW' => 5, // 土地に対する星評価
            'LAND_COMMENT' => '思ったより広くて、イベント開催に最適でした。周りの環境も落ち着いていて良いです。',
            'USER_REVIEW' => 4, // オーナーに対する星評価
            'USER_COMMENT' => 'オーナーの対応が丁寧で、期間の延長相談にも柔軟に対応していただきました。',
            'DATE' => now()->subDays(1)->format('Y-m-d'),
            'USER_ID' => 1, // レビュー者（レンタル者）
            'LAND_ID' => 2,
            'RECORD_ID' => 2,
        ]);

        // RECORD_ID=3のレンタル（30日前～25日前）にレビューを付与
        ReviewComment::create([
            'LAND_REVIEW' => 4,
            'LAND_COMMENT' => 'きれいに整備されていて、作業しやすかったです。水道が近くにあるのが便利。',
            'USER_REVIEW' => 5,
            'USER_COMMENT' => '初心者向けのアドバイスをいただき、とても親切でした。また利用したいです。',
            'DATE' => now()->subDays(25)->format('Y-m-d'),
            'USER_ID' => 1,
            'LAND_ID' => 3,
            'RECORD_ID' => 3,
        ]);

        $this->command->info('テスト用レンタル記録（3件）とレビュー（2件）を登録しました！');
    }
}
