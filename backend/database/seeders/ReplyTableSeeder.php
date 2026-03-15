<?php

/**
 * ============================================================
 * 返信テーブルシーダー (ReplyTableSeeder.php)
 * ============================================================
 * REPLY_TABLE を初期化し、問い合わせ1件につき1件の返信（計50件）を登録するシーダー。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=ReplyTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Member;
use App\Models\Reply;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReplyTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        Schema::disableForeignKeyConstraints();
        DB::table('REPLY_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        $users = Member::whereIn('EMAIL', ['userA@example.com', 'userB@example.com'])->get();
        $contacts = Contact::all();

        foreach ($contacts as $contact) {
            $responder = $faker->randomElement($users);
            Reply::create([
                'CONTACT_ID' => $contact->CONTACT_ID,
                'USER_ID' => $responder->USER_ID,
                'MESSAGE' => $faker->realText(100),
                'DATE' => Carbon::parse($contact->DATE)->addDays(1),
            ]);
        }
    }
}
