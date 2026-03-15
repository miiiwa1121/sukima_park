<?php
/**
 * ============================================================
 * テスト土地シーダー (TestLandSeeder.php)
 * ============================================================
 * 
 * 開発・テスト用の土地データを5件登録するSeeder
 * 
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=TestLandSeeder
 * 
 * 【注意】
 * - 本番環境では実行しないでください
 * - USER_ID=1（テストユーザー）の土地として登録されます
 * 
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Land;
use Illuminate\Database\Seeder;

class TestLandSeeder extends Seeder
{
    /**
     * テスト用の土地データを登録
     */
    public function run(): void
    {
        // 既存の土地データを削除（オプション：コメント解除で有効化）
        // Land::where('USER_ID', 1)->delete();

        // テスト用土地データ（5件）
        $lands = [
            [
                'NAME' => '都市型ポップアップスペース',
                'PEREFECTURES' => 13, // 東京都
                'CITY' => '中央区',
                'STREET_ADDRESS' => '日本橋1-2-3',
                'AREA' => 80.00,
                'TITLE_DEED' => 'title_deed_1.pdf', // 権利証ファイル
                'DESCRIPTION' => 'ビル屋上のフラットスペース。イベントや期間限定ショップ向けとして利用可能です。電源3口、Wi-Fi完備。',
                'PRICE' => 20000,
                'PRICE_UNIT' => 0, // 日あたり
                'USER_ID' => 1,
                'STATUS' => 1, // 公開中
            ],
            [
                'NAME' => '里山キャンプベース',
                'PEREFECTURES' => 20, // 長野県
                'CITY' => '茅野市',
                'STREET_ADDRESS' => '豊平4567',
                'AREA' => 450.00,
                'TITLE_DEED' => 'title_deed_2.pdf',
                'DESCRIPTION' => '標高1,000mの涼しいキャンプ用地。電源・簡易トイレあり。グループキャンプやワーケーションに最適。',
                'PRICE' => 15000,
                'PRICE_UNIT' => 0, // 日あたり
                'USER_ID' => 1,
                'STATUS' => 0, // 非公開
            ],
            [
                'NAME' => '街角コミュニティガーデン',
                'PEREFECTURES' => 11, // 埼玉県
                'CITY' => '川口市',
                'STREET_ADDRESS' => '本町2-15-8',
                'AREA' => 50.00,
                'TITLE_DEED' => 'title_deed_3.pdf',
                'DESCRIPTION' => '地域向けに週末菜園として利用されています。水道・倉庫あり。初心者歓迎！',
                'PRICE' => 5000,
                'PRICE_UNIT' => 0, // 日あたり
                'USER_ID' => 1,
                'STATUS' => 1, // 公開中
            ],
            [
                'NAME' => '駅前小規模駐車場',
                'PEREFECTURES' => 14, // 神奈川県
                'CITY' => '横浜市西区',
                'STREET_ADDRESS' => '南幸1-10-5',
                'AREA' => 30.00,
                'TITLE_DEED' => 'title_deed_4.pdf',
                'DESCRIPTION' => '駅から徒歩3分の好立地。車2台分のスペース。イベント時の荷物搬入拠点としても利用可能。',
                'PRICE' => 3000,
                'PRICE_UNIT' => 0, // 日あたり
                'USER_ID' => 1,
                'STATUS' => 1, // 公開中
            ],
            [
                'NAME' => '郊外イベント広場',
                'PEREFECTURES' => 12, // 千葉県
                'CITY' => '柏市',
                'STREET_ADDRESS' => '若葉台5-20',
                'AREA' => 200.00,
                'TITLE_DEED' => 'title_deed_5.pdf',
                'DESCRIPTION' => '広々とした芝生の広場。フリーマーケットや野外ライブに最適。駐車場20台分あり。',
                'PRICE' => 50000,
                'PRICE_UNIT' => 0, // 日あたり
                'USER_ID' => 1,
                'STATUS' => 0, // 非公開
            ],
        ];


        foreach ($lands as $landData) {
            Land::create($landData);
        }

        $this->command->info('テスト用土地データを5件登録しました！');
    }
}
