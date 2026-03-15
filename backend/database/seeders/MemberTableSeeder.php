<?php

/**
 * ============================================================
 * 会員テーブルシーダー (MemberTableSeeder.php)
 * ============================================================
 * MEMBER_TABLE を初期化して、テスト用ユーザを投入するシーダー。
 * パスワードは指定の平文をハッシュ化して保存する。
 *
 * 【テストアカウント一覧】
 * - Email: userA@example.com, Password: A1234567890 (通常ユーザ)
 * - Email: userB@example.com, Password: B1234567890 (通常ユーザ)
 * - Email: admin@example.com, Password: admin1234567890 (管理者)
 * - Email: BAN@example.com, Password: BAN1234567890 (凍結ユーザ)
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=MemberTableSeeder
 *
 * 【注意】
 * - 本番環境では実行しないでください
 * - 既存データは truncate されます
 * ============================================================
 */

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MemberTableSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        // 既存データをクリア
        DB::table('MEMBER_TABLE')->truncate();
        Schema::enableForeignKeyConstraints();

        // ユーザA～Zを作成（26人）
        for ($i = 0; $i < 26; $i++) {
            $letter = chr(65 + $i); // A～Z
            $num = str_pad($i + 1, 4, '0', STR_PAD_LEFT); // 0001～0026
            
            Member::create([
                'EMAIL' => "user{$letter}@example.com",
                'PASSWORD' => Hash::make("{$letter}1234567890"),
                'TEL' => "090-0000-{$num}",
                'BIRTH' => sprintf('1980-%02d-15', ($i % 12) + 1),
                'SHOW_BIRTH' => true,
                'GENDER' => $i % 3,
                'SHOW_GENDER' => true,
                'IDENTITY' => "id_card_{$letter}.png",
                'USERNAME' => "User {$letter}",
                'SELF_INTRODUCTION' => "Seeder user {$letter}",
                'ICON_IMAGE' => 'default_icon.png',
                'ACCOUNT_STATUS' => 0,
            ]);
        }

        // 管理者ユーザ
        Member::create([
            'EMAIL' => 'admin@example.com',
            'PASSWORD' => Hash::make('admin1234567890'),
            'TEL' => '090-0000-0000',
            'BIRTH' => '1985-01-01',
            'SHOW_BIRTH' => false,
            'GENDER' => 0,
            'SHOW_GENDER' => false,
            'IDENTITY' => 'admin_id.png',
            'USERNAME' => '管理者',
            'SELF_INTRODUCTION' => 'System Administrator',
            'ICON_IMAGE' => 'default_icon.png',
            'ACCOUNT_STATUS' => 2
        ]);

        // 凍結ユーザ
        Member::create([
            'EMAIL' => 'BAN@example.com',
            'PASSWORD' => Hash::make('BAN1234567890'),
            'TEL' => '090-0000-9999',
            'BIRTH' => '1988-03-10',
            'SHOW_BIRTH' => false,
            'GENDER' => 0,
            'SHOW_GENDER' => false,
            'IDENTITY' => 'ban_id.png',
            'USERNAME' => '凍結ユーザ',
            'SELF_INTRODUCTION' => 'This account is suspended',
            'ICON_IMAGE' => 'default_icon.png',
            'ACCOUNT_STATUS' => 1
        ]);
    }
}
