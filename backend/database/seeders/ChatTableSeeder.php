<?php

/**
 * ============================================================
 * チャットテーブルシーダー (ChatTableSeeder.php)
 * ============================================================
 * CHAT_TABLE を初期化し、テスト用メッセージを投入するシーダー。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=ChatTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChatTableSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('CHAT_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        // テストユーザーを取得
        $userA = Member::where('EMAIL', 'userA@example.com')->first();
        $userB = Member::where('EMAIL', 'userB@example.com')->first();

        if (!$userA || !$userB) {
            $this->command->warn('テストユーザーが見つかりません。MemberTableSeederを先に実行してください。');
            return;
        }

        $now = Carbon::now();

        // UserAとUserBの会話
        $this->createMessage($userA->USER_ID, $userB->USER_ID, 'こんにちは！お問い合わせありがとうございます。', $now->copy()->subDays(2)->setTime(14, 23));
        $this->createMessage($userB->USER_ID, $userA->USER_ID, 'こんにちは。ご返信ありがとうございます。', $now->copy()->subDays(2)->setTime(14, 25));
        $this->createMessage($userB->USER_ID, $userA->USER_ID, '詳細について教えていただけますでしょうか？', $now->copy()->subDays(2)->setTime(14, 25));
        $this->createMessage($userA->USER_ID, $userB->USER_ID, 'もちろんです！具体的にはどのような内容についてお知りになりたいですか？', $now->copy()->subDays(2)->setTime(14, 30));
        $this->createMessage($userA->USER_ID, $userB->USER_ID, 'おはようございます！昨日の件ですが、貸出について準備しました。', $now->copy()->subDays(1)->setTime(9, 15));
        $this->createMessage($userB->USER_ID, $userA->USER_ID, 'ありがとうございます！', $now->copy()->subDays(1)->setTime(9, 20));
        $this->createMessage($userA->USER_ID, $userB->USER_ID, 'また連絡させていただきます。よろしくお願いします。', $now->copy()->subHours(2));
    }

    /**
     * メッセージを作成
     */
    private function createMessage(int $fromId, int $toId, string $message, Carbon $dateTime): void
    {
        Chat::create([
            'USER_ID_FROM' => $fromId,
            'USER_ID_TO' => $toId,
            'MESSAGE' => $message,
            'IMAGE' => null,
            'YEAR' => $dateTime->format('Y-m-d'),
            'DATE' => $dateTime->format('Y-m-d'),
            'TIME' => $dateTime->format('H:i:s'),
        ]);
    }
}
