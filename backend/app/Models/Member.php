<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * ============================================================
 * 会員モデル (Member)
 * ============================================================
 * 
 * 【このモデルの役割】
 * MEMBER_TABLEテーブルと対応するEloquentモデルです。
 * Authenticatableを継承しているため、Laravel認証システムで
 * ログイン対象ユーザーとして使用できます。
 * 
 * 【認証との関係】
 * - config/auth.phpのprovidersで、このモデルが認証ユーザーとして指定されています
 * - getAuthPassword()メソッドでパスワードカラム名をカスタマイズしています
 * 
 * ============================================================
 */
class Member extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    /**
     * ============================================================
     * データベース設定
     * ============================================================
     */

    /**
     * このモデルが接続するテーブル名
     * デフォルトは「members」だが、既存DBに合わせて「MEMBER_TABLE」を使用
     */
    protected $table = 'MEMBER_TABLE';

    /**
     * 主キー（プライマリキー）のカラム名
     * デフォルトは「id」だが、既存DBに合わせて「USER_ID」を使用
     */
    protected $primaryKey = 'USER_ID';

    /**
     * タイムスタンプ（created_at, updated_at）を自動管理するか
     * false = 自動管理しない（既存DBにこれらのカラムがないため）
     */
    public $timestamps = false;

    /**
     * ============================================================
     * マスアサインメント設定
     * ============================================================
     * 
     * $fillable: create()やupdate()で一括代入を許可するカラム
     * セキュリティ上、明示的に許可されたカラムのみ一括代入可能
     */
    protected $fillable = [
        'EMAIL',              // メールアドレス
        'PASSWORD',           // パスワード（ハッシュ化済み）
        'TEL',                // 電話番号
        'BIRTH',              // 生年月日
        'SHOW_BIRTH',         // 生年月日を公開するか
        'GENDER',             // 性別（0:未設定, 1:男性, 2:女性）
        'SHOW_GENDER',        // 性別を公開するか
        'IDENTITY_IMAGE',     // 本人確認書類の画像パス
        'USERNAME',           // ユーザー名（表示名）
        'SELF_INTRODUCTION',  // 自己紹介文
        'ICON_IMAGE',         // プロフィール画像のパス
        'ACCOUNT_STATUS',     // アカウント状態（0:正常, 1:凍結など）
    ];

    /**
     * ============================================================
     * 非表示カラム設定
     * ============================================================
     * 
     * $hidden: JSONや配列に変換時に非表示にするカラム
     * パスワードなどのセキュリティ上公開すべきでない情報を指定
     */
    protected $hidden = [
        'PASSWORD',  // パスワードは絶対に公開しない
    ];

    /**
     * ============================================================
     * 型キャスト設定
     * ============================================================
     * 
     * $casts: データベースの値をPHPの型に自動変換する設定
     * 例: 文字列 '1' → boolean true
     */
    protected $casts = [
        'BIRTH' => 'date',           // Carbonインスタンスに変換（日付操作が容易に）
        'SHOW_BIRTH' => 'boolean',   // 0/1 → false/true
        'SHOW_GENDER' => 'boolean',  // 0/1 → false/true
        'ACCOUNT_STATUS' => 'integer', // 整数に変換
    ];

    /**
     * ============================================================
     * Laravel認証用メソッド
     * ============================================================
     */

    /**
     * Laravel認証用：パスワードを取得するメソッド
     * 
     * 【なぜ必要か】
     * Laravelの認証システムはデフォルトで「password」カラムを期待しますが、
     * このアプリでは「PASSWORD」という大文字のカラム名を使用しているため、
     * このメソッドをオーバーライドして正しいカラムを参照させます。
     *
     * @return string ハッシュ化されたパスワード
     */
    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }

    /**
     * Laravel認証用：パスワードリセットメール送信先を取得するメソッド
     * 
     * 【なぜ必要か】
     * パスワードリセット機能で、どのメールアドレスにリセットリンクを
     * 送信するかを指定します。カラム名が「EMAIL」のためオーバーライドが必要。
     *
     * @return string メールアドレス
     */
    public function getEmailForPasswordReset()
    {
        return $this->EMAIL;
    }

    /**
     * ============================================================
     * リレーション定義
     * ============================================================
     * 
     * 【リレーションとは】
     * テーブル間の関連（1対多、多対多など）を定義する機能
     * 例: 1人の会員が複数の土地を所有 → hasMany（1対多）
     */

    /**
     * この会員が所有する土地を取得
     * 
     * 【リレーション】1対多（1人の会員 → 複数の土地）
     * 【外部キー】MEMBER_TABLE.USER_ID ← → LAND_TABLE.USER_ID
     */
    public function lands()
    {
        return $this->hasMany(Land::class, 'USER_ID', 'USER_ID');
    }

    /**
     * この会員の貸出記録を取得
     * 
     * 【リレーション】1対多（1人の会員 → 複数の貸出記録）
     */
    public function rentalRecords()
    {
        return $this->hasMany(RentalRecord::class, 'USER_ID', 'USER_ID');
    }

    /**
     * この会員の問い合わせを取得
     * 
     * 【リレーション】1対多（1人の会員 → 複数の問い合わせ）
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'USER_ID', 'USER_ID');
    }

    /**
     * この会員が送信したチャットを取得
     * 
     * 【リレーション】1対多（1人の会員 → 複数の送信チャット）
     * 【外部キー】USER_ID_FROM（送信者ID）
     */
    public function sentChats()
    {
        return $this->hasMany(Chat::class, 'USER_ID_FROM', 'USER_ID');
    }

    /**
     * この会員が受信したチャットを取得
     * 
     * 【リレーション】1対多（1人の会員 → 複数の受信チャット）
     * 【外部キー】USER_ID_TO（受信者ID）
     */
    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'USER_ID_TO', 'USER_ID');
    }

    /**
     * ============================================================
     * アクセサ（Accessor）
     * ============================================================
     */

    // 会員名を取得（Bladeで$member->nameでアクセス可能）
    public function getNameAttribute()
    {
        return $this->attributes['USERNAME'] ?? null;
    }
}
