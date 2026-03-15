<?php

/**
 * ============================================================
 * 土地テーブルシーダー (LandTableSeeder.php)
 * ============================================================
 * LAND_TABLE を初期化し、ユーザA/Bそれぞれに50件ずつ土地を登録するシーダー。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=LandTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Land;
use App\Models\Member;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LandTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        Schema::disableForeignKeyConstraints();
        DB::table('LAND_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        $owners = Member::whereIn('EMAIL', ['userA@example.com', 'userB@example.com'])->get();

        foreach ($owners as $owner) {
            for ($i = 1; $i <= 50; $i++) {
                Land::create([
                    'NAME' => "Land {$owner->USER_ID}-{$i}",
                    'PEREFECTURES' => $faker->numberBetween(1, 47),
                    'CITY' => $faker->city,
                    'STREET_ADDRESS' => $faker->streetAddress,
                    'AREA' => $faker->randomFloat(2, 10, 200),
                    'IMAGE' => null,
                    'TITLE_DEED' => "title_deed_{$owner->USER_ID}_{$i}.pdf",
                    'DESCRIPTION' => $faker->realText(80),
                    'RENTAL_START_DATE' => Carbon::now()->subDays(30),
                    'RENTAL_END_DATE' => Carbon::now()->addDays(180),
                    'RENTAL_START_TIME' => '08:00',
                    'RENTAL_END_TIME' => '20:00',
                    'PRICE' => $faker->numberBetween(2000, 8000),
                    'PRICE_UNIT' => $faker->numberBetween(0, 2),
                    'USER_ID' => $owner->USER_ID,
                    'STATUS' => true,
                ]);
            }
        }
    }
}
