<?php

/**
 * ============================================================
 * レビュー・コメントテーブルシーダー (ReviewCommentTableSeeder.php)
 * ============================================================
 * REVIEW_COMMENT_TABLE を初期化し、貸出記録の半数（5件）にレビューを付与するシーダー。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=ReviewCommentTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Member;
use App\Models\RentalRecord;
use App\Models\ReviewComment;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewCommentTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        Schema::disableForeignKeyConstraints();
        DB::table('REVIEW_COMMENT_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        $rentalRecords = RentalRecord::all()->shuffle()->take(5);
        $users = Member::whereIn('EMAIL', ['userA@example.com', 'userB@example.com'])->get();

        foreach ($rentalRecords as $record) {
            $reviewer = $faker->randomElement($users);
            ReviewComment::create([
                'LAND_REVIEW' => $faker->numberBetween(3, 5),
                'LAND_COMMENT' => $faker->realText(100),
                'USER_REVIEW' => $faker->numberBetween(3, 5),
                'USER_COMMENT' => $faker->realText(80),
                'DATE' => Carbon::parse($record->RENTAL_END_DATE)->addDays(1),
                'USER_ID' => $reviewer->USER_ID,
                'LAND_ID' => $record->LAND_ID,
                'RECORD_ID' => $record->RECORD_ID,
            ]);
        }
    }
}
