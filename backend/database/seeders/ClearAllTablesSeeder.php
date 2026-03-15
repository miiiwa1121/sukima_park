<?php

/**
 * ============================================================
 * 全テーブル削除シーダー (ClearAllTablesSeeder.php)
 * ============================================================
 * すべてのテーブルのデータを削除（truncate）するシーダー。
 * 開発時にデータベースをリセットしたい場合に使用します。
 *
 * 【使い方】
 * docker compose exec laravel.test php artisan db:seed --class=ClearAllTablesSeeder
 *
 * 【注意】
 * - 本番環境では絶対に実行しないでください
 * - すべてのテーブルのデータが削除されます
 * - この処理は取り消せません
 * ============================================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAllTablesSeeder extends Seeder
{
    public function run(): void
    {
        // 外部キー制約を一時的に無効化
        Schema::disableForeignKeyConstraints();

        // すべてのテーブルをtruncate
        $tables = [
            'REVIEW_COMMENT_TABLE',  // 外部キー参照があるため先に削除
            'REPLY_TABLE',
            'RENTAL_RECORD_TABLE',
            'CONTACT_TABLE',
            'LAND_TABLE',
            'MEMBER_TABLE',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->info("✓ {$table} のデータを削除しました");
        }

        // 外部キー制約を復旧
        Schema::enableForeignKeyConstraints();

        $this->command->info('すべてのテーブルをクリアしました！');
    }
}
