<?php

/**
 * ============================================================
 * 問い合わせテーブルシーダー (ContactTableSeeder.php)
 * ============================================================
 * CONTACT_TABLE を初期化し、50件の問い合わせを投入するシーダー。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=ContactTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Member;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ContactTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        Schema::disableForeignKeyConstraints();
        DB::table('CONTACT_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        $users = Member::whereIn('EMAIL', ['userA@example.com', 'userB@example.com'])->get();

        for ($i = 1; $i <= 50; $i++) {
            $sender = $faker->randomElement($users);
            Contact::create([
                'TITLE' => "Contact {$i}",
                'MESSAGE' => $faker->realText(120),
                'USER_ID' => $sender->USER_ID,
                'DATE' => Carbon::now()->subDays($faker->numberBetween(0, 60)),
                'STATUS' => $faker->numberBetween(0, 2),
            ]);
        }
    }
}
