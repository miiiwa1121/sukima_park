{{--
============================================================
ログイン画面 (auth/login.blade.php)
============================================================

【このビューの役割】
会員ログイン用のフォーム画面を表示します。

【機能】
- メールアドレスとパスワードの入力
- 「ログイン状態を保持する」チェックボックス（Remember Me機能）
- パスワード再設定へのリンク
- 新規会員登録へのリンク

【URLとルート】
- 表示: GET /login (ルート名: login)
- 送信: POST /login (AuthController@login)

============================================================
--}}

@extends('layouts.app')

@section('title', 'ログイン')

{{-- ページ固有のスタイル --}}
@push('styles')
<style>
    /* ============================================================
       ログインセクション全体のスタイル
       ============================================================ */
    .login-section {
        padding: 60px 0;
        min-height: calc(100vh - 200px);   /* ヘッダーとフッター分を引いた高さ */
        display: flex;
        align-items: center;               /* 垂直方向の中央揃え */
    }

    /* ============================================================
       フォームコンテナ（白い背景のカード部分）
       ============================================================ */
    .form-container {
        background: var(--bg-white, #fff);
        border-radius: 8px;                /* 角丸 */
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);  /* 軽い影 */
        max-width: 480px;
        margin: 0 auto;                    /* 左右中央揃え */
        width: 100%;
    }

    /* フォームタイトル */
    .form-container h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #222;
        text-align: center;
    }

    /* フォームサブタイトル */
    .form-subtitle {
        text-align: center;
        color: #888;
        font-size: 14px;
        margin-bottom: 32px;
    }

    /* ============================================================
       フォームグループ（ラベル + 入力欄のセット）
       ============================================================ */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #555;
    }

    /* 必須マーク */
    .required {
        color: #d32f2f;  /* 赤色 */
        font-size: 12px;
        margin-left: 4px;
    }

    /* ============================================================
       入力フィールドのスタイル
       ============================================================ */
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;     /* フォーカス時のアニメーション */
    }

    /* フォーカス時のスタイル */
    .form-control:focus {
        outline: none;
        border-color: #2e7d32;             /* 緑色の枠線 */
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);  /* 緑色の光彩 */
    }

    /* バリデーションエラー時のスタイル */
    .form-control.is-invalid {
        border-color: #d32f2f;             /* 赤い枠線 */
    }

    /* エラーメッセージ */
    .invalid-feedback {
        font-size: 12px;
        color: #d32f2f;
        margin-top: 4px;
        display: block;
    }

    /* ============================================================
       全体のエラーメッセージ（認証失敗時）
       ============================================================ */
    .general-error {
        background: #ffebee;               /* 薄い赤背景 */
        color: #d32f2f;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 14px;
    }

    /* ============================================================
       パスワードオプション（Remember MeとパスワードResetリンク）
       ============================================================ */
    .password-options {
        display: flex;
        justify-content: space-between;    /* 左右に配置 */
        align-items: center;
        margin-top: 8px;
        font-size: 13px;
    }

    /* 「ログイン状態を保持する」チェックボックス */
    .remember-me {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        color: #555;
    }

    .remember-me input[type="checkbox"] {
        cursor: pointer;
        accent-color: #2e7d32;             /* チェックボックスの色 */
    }

    /* パスワードをお忘れですかリンク */
    .forgot-password {
        color: #2e7d32;
        text-decoration: none;
        font-weight: 500;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    /* ============================================================
       送信ボタン
       ============================================================ */
    .submit-btn {
        width: 100%;
        padding: 12px;
        background: #2e7d32;               /* 緑色 */
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 24px;
        transition: all 0.2s;
    }

    .submit-btn:hover {
        background: #1b5e20;               /* ホバー時は濃い緑 */
    }

    /* ============================================================
       区切り線（「または」の部分）
       ============================================================ */
    .divider {
        text-align: center;
        margin: 24px 0;
        position: relative;
    }

    .divider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e0e0e0;               /* グレーの線 */
    }

    .divider span {
        background: #fff;
        padding: 0 16px;
        color: #888;
        font-size: 13px;
        position: relative;
        z-index: 1;                        /* 線の上に表示 */
    }

    /* ============================================================
       新規登録リンク
       ============================================================ */
    .register-link {
        text-align: center;
        font-size: 14px;
        color: #555;
    }

    .register-link a {
        color: #2e7d32;
        text-decoration: none;
        font-weight: 500;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    /* ============================================================
       レスポンシブ対応（スマートフォン用）
       ============================================================ 
    @media (max-width: 768px) {
        .login-section {
            padding: 40px 0;
        }

        .form-container {
            padding: 32px 24px;
        }

        .form-container h1 {
            font-size: 22px;
        }

        /* パスワードオプションを縦並びに 
        .password-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
    */
</style>
@endpush

@section('content')
<section class="login-section">
    <div class="form-container">
        <h1>ログイン</h1>
        <p class="form-subtitle">アカウントにログインしてください</p>

        {{-- 
            エラーメッセージ表示（認証失敗時）
            AuthControllerで back()->withErrors() として設定されたエラーを表示
        --}}
        @if ($errors->has('email') && !$errors->first('email') == '必須項目です')
            <div class="general-error">
                {{ $errors->first('email') }}
            </div>
        @endif

        {{-- 
            ログインフォーム
            - action: POST /login へ送信
            - @csrf: CSRF対策用のトークンを自動挿入
        --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- 
                メールアドレス入力欄
                - name="email": AuthController側で $request->email として取得
                - old('email'): バリデーションエラー時に入力値を保持
            --}}
            <div class="form-group">
                <label for="email">
                    メールアドレス<span class="required">必須</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="メールアドレスを入力"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
                {{-- 
                    バリデーションエラー表示
                    @error('フィールド名') で該当フィールドのエラーを取得
                --}}
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- 
                パスワード入力欄
                - type="password": 入力文字を非表示にする
                - autocomplete="current-password": ブラウザの自動入力を許可
            --}}
            <div class="form-group">
                <label for="password">
                    パスワード<span class="required">必須</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="パスワードを入力"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- 
                パスワードオプション
                - Remember Me チェックボックス
                - パスワードリセットリンク
            --}}
            <div class="password-options">
                {{-- 
                    「ログイン状態を保持する」チェックボックス
                    - name="remember": AuthControllerで $request->filled('remember') として取得
                    - チェックされた状態で送信されると、ブラウザを閉じても
                      30日間ログイン状態が維持される（config/auth.phpで設定）
                --}}
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>ログイン状態を保持する</span>
                </label>
                {{-- パスワードリセット機能（現在は仮実装） --}}
                <a href="#" class="forgot-password" onclick="alert('パスワード再設定用のメールを送信しました。'); return false;">パスワードをお忘れですか？</a>
            </div>

            {{-- 送信ボタン --}}
            <button type="submit" class="submit-btn">ログイン</button>
        </form>

        {{-- 区切り線 --}}
        <div class="divider">
            <span>または</span>
        </div>

        {{-- 
            新規登録リンク
            route('register') は routes/web.php で定義された
            /register へのリンクを生成
        --}}
        <div class="register-link">
            アカウントをお持ちでない方は
            <a href="{{ route('register') }}">新規登録</a>
        </div>
    </div>
</section>
@endsection
