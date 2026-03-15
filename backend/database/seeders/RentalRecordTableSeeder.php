<?php

/**
 * ============================================================
 * 貸出記録テーブルシーダー (RentalRecordTableSeeder.php)
 * ============================================================
 * RENTAL_RECORD_TABLE を初期化し、10件の貸出記録を登録するシーダー。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=RentalRecordTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Land;
use App\Models\Member;
use App\Models\RentalRecord;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RentalRecordTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        Schema::disableForeignKeyConstraints();
        DB::table('RENTAL_RECORD_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        $lands = Land::all();
        $users = Member::whereIn('EMAIL', ['userA@example.com', 'userB@example.com'])->get();

        for ($i = 0; $i < 10; $i++) {
            $land = $faker->randomElement($lands);
            $landOwnerId = $land->USER_ID;
            
            // 持ち主以外のユーザーを借り手として選択
            $renter = $users->where('USER_ID', '!=', $landOwnerId)->random();

            $start = Carbon::today()->subDays($faker->numberBetween(1, 60));
            $end = (clone $start)->addDays($faker->numberBetween(1, 14));

            RentalRecord::create([
                'PRICE' => $land->PRICE,
                'PRICE_UNIT' => $land->PRICE_UNIT,
                'RENTAL_START_DATE' => $start->toDateString(),
                'RENTAL_END_DATE' => $end->toDateString(),
                'RENTAL_START_TIME' => '09:00',
                'RENTAL_END_TIME' => '18:00',
                'LAND_ID' => $land->LAND_ID,
                'USER_ID' => $renter->USER_ID,
            ]);
        }
    }
}
