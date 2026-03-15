{{--
============================================================
会員登録画面 (auth/register.blade.php)
============================================================

【このビューの役割】
新規会員登録用のフォーム画面を表示します。

【入力項目】
- ログインID（必須）: 4〜20文字の半角英数記号
- メールアドレス（必須）: 重複不可
- パスワード（必須）: 8〜20文字、英字と数字の両方を含む
- パスワード確認（必須）: パスワードと一致
- 電話番号（必須）: 10〜11桁の数字
- 生年月日（必須）
- 性別（必須）
- 本人確認書類（必須）: 画像ファイル（5MB以下）

【URLとルート】
- 表示: GET /register (ルート名: register)
- 送信: POST /register (AuthController@register)

【バリデーション】
- フロントエンド: JavaScript（リアルタイムチェック）
- バックエンド: AuthController内のvalidate()

============================================================
--}}

@extends('layouts.app')

@section('title', '会員登録')

@section('content')

{{-- ページ固有のスタイル --}}
<style>
      /* ============================================================
         CSS変数（デザインの統一に使用）
         ============================================================ */
      :root {
        --primary-color: #2e7d32;        /* メインカラー（緑） */
        --primary-hover: #1b5e20;        /* ホバー時のメインカラー */
        --error-color: #d32f2f;          /* エラー表示用（赤） */
        --border-color: #e0e0e0;         /* 枠線の色 */
        --text-primary: #333;            /* メインテキスト色 */
        --text-secondary: #555;          /* サブテキスト色 */
        --text-hint: #888;               /* ヒントテキスト色 */
        --bg-page: #fafafa;              /* ページ背景色 */
        --bg-white: #fff;                /* 白背景 */
        --bg-secondary: #f5f5f5;         /* セカンダリ背景 */
        --bg-hover: #e0e0e0;             /* ホバー時背景 */
        --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);  /* 影 */
        --border-radius: 6px;            /* 角丸 */
        --transition: all 0.2s;          /* アニメーション設定 */
      }

      /* ============================================================
         リセットCSS（ブラウザのデフォルトスタイルをリセット）
         ============================================================ */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      /* ============================================================
         ベーススタイル
         ============================================================ */
      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
          "Hiragino Sans", sans-serif;
        line-height: 1.6;
        color: var(--text-primary);
        background: var(--bg-page);
      }

      /* コンテナ（中央寄せ用） */
      .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
      }

      /* ============================================================
         ヘッダースタイル
         ============================================================ */
      header {
        background: var(--bg-white);
        border-bottom: 1px solid var(--border-color);
        position: sticky;              /* スクロールしても上部に固定 */
        top: 0;
        z-index: 100;                  /* 他の要素より前面に表示 */
      }

      .header-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
      }

      /* ロゴ */
      .logo {
        font-size: 18px;
        font-weight: 600;
        color: var(--primary-color);
        text-decoration: none;
      }

      /* ヘッダーナビゲーション */
      .header-nav {
        display: flex;
        gap: 12px;
        align-items: center;
      }

      /* ============================================================
         ボタンスタイル
         ============================================================ */
      .btn {
        padding: 8px 16px;
        border-radius: var(--border-radius);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
      }

      /* プライマリボタン（メインアクション用） */
      .btn-primary {
        background: var(--primary-color);
        color: var(--bg-white);
      }

      .btn-primary:hover {
        background: var(--primary-hover);
      }

      /* セカンダリボタン（サブアクション用） */
      .btn-secondary {
        background: var(--bg-secondary);
        color: var(--text-primary);
      }

      .btn-secondary:hover {
        background: var(--bg-hover);
      }

      /* ============================================================
         フォームスタイル
         ============================================================ */
      .registration-section {
        padding: 40px 0;
      }

      /* フォームコンテナ（白い背景のカード） */
      .form-container {
        background: var(--bg-white);
        border-radius: 8px;
        padding: 32px;
        box-shadow: var(--shadow);
        max-width: 600px;
        margin: 0 auto;
      }

      /* フォームタイトル */
      .form-container h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #222;
        text-align: center;
      }

      /* フォームグループ（ラベル + 入力欄のセット） */
      .form-group {
        margin-bottom: 20px;
      }

      .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
        color: var(--text-secondary);
      }

      /* 必須マーク */
      .required {
        color: var(--error-color);
        font-size: 12px;
        margin-left: 4px;
      }

      /* 入力フィールド共通スタイル */
      .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
      }

      /* フォーカス時のスタイル */
      .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
      }

      /* エラー時のスタイル */
      .form-control.error {
        border-color: var(--error-color);
      }

      /* ファイル入力のスタイル */
      .form-control[type="file"] {
        padding: 8px 12px;
      }

      /* 入力ヒント（補足説明） */
      .form-control-hint {
        font-size: 12px;
        color: var(--text-hint);
        margin-top: 4px;
      }

      /* エラーメッセージ */
      .error-message {
        font-size: 12px;
        color: var(--error-color);
        margin-top: 4px;
        display: none;              /* 初期状態は非表示 */
      }

      .error-message.show {
        display: block;             /* showクラスで表示 */
      }

      /* ============================================================
         ラジオボタンスタイル
         ============================================================ */
      .radio-group {
        display: flex;
        gap: 16px;
        margin-top: 8px;
        flex-wrap: wrap;            /* 画面幅が狭い時に折り返す */
      }

      .radio-label {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: normal;
      }

      .radio-label input[type="radio"] {
        cursor: pointer;
        accent-color: var(--primary-color);  /* チェック時の色 */
      }

      /* ============================================================
         送信ボタン
         ============================================================ */
      .submit-btn {
        width: 100%;
        padding: 12px;
        background: var(--primary-color);
        color: var(--bg-white);
        border: none;
        border-radius: var(--border-radius);
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 16px;
        transition: var(--transition);
      }

      .submit-btn:hover:not(:disabled) {
        background: var(--primary-hover);
      }

      /* 送信中（disabled時）のスタイル */
      .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }

      /* ============================================================
         ログインリンク
         ============================================================ */
      .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
      }

      .login-link a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
      }

      .login-link a:hover {
        text-decoration: underline;
      }

      /* ============================================================
         成功メッセージ（登録完了時）
         ============================================================ */
      .success-message {
        background: #e8f5e9;         /* 薄い緑背景 */
        color: var(--primary-color);
        padding: 12px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
        display: none;               /* 初期状態は非表示 */
        text-align: center;
      }

      .success-message.show {
        display: block;
      }

      /* ============================================================
         レスポンシブ対応（スマートフォン用）
         ============================================================ */
      @media (max-width: 768px) {
        .header-nav {
          gap: 8px;
        }

        .btn {
          padding: 6px 12px;
          font-size: 13px;
        }

        .form-container {
          padding: 24px 16px;
        }

        .form-container h1 {
          font-size: 20px;
        }

        /* ラジオボタンを縦並びに */
        .radio-group {
          flex-direction: column;
          gap: 8px;
        }
      }
    </style>

    <main>
      <section class="registration-section">
        <div class="container">
          <div class="form-container">
            <h1>会員登録</h1>

            {{-- 成功メッセージ（JavaScriptで表示制御） --}}
            <div class="success-message" id="successMessage">
              登録が完了しました！
            </div>

            {{-- サーバー側バリデーションエラー表示 --}}
            @if ($errors->any())
            <div style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
              <strong>エラーが発生しました：</strong>
              <ul style="margin: 8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif

            {{--
              会員登録フォーム
              - method="POST": データをPOSTで送信
              - action: ルート名'register'（/register）に送信
              - enctype="multipart/form-data": ファイルアップロードに必要
              - novalidate: ブラウザのデフォルトバリデーションを無効化
                           （JavaScriptでカスタムバリデーションを行うため）
            --}}
            <form id="registrationForm" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
              {{-- CSRF対策トークン（Laravelで必須） --}}
              @csrf

              {{-- ログインID入力欄 --}}
              <div class="form-group">
                <label for="loginId"
                  >ログインID<span class="required">必須</span></label
                >
                <input
                  type="text"
                  id="loginId"
                  name="username"
                  class="form-control"
                  placeholder="例: sukimapark_user"
                  required
                  pattern="[a-zA-Z0-9_]+"
                  minlength="4"
                  maxlength="20"
                  autocomplete="username"
                  value="{{ old('username') }}"
                />
                <p class="form-control-hint">
                  4〜20文字の半角英数記号（_）が使えます
                </p>
                <p class="error-message" id="loginIdError"></p>
              </div>

              {{-- メールアドレス入力欄 --}}
              <div class="form-group">
                <label for="email"
                  >メールアドレス<span class="required">必須</span></label
                >
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-control"
                  placeholder="例: user@example.com"
                  required
                  autocomplete="email"
                  value="{{ old('email') }}"
                />
                <p class="error-message" id="emailError"></p>
              </div>

              {{-- パスワード入力欄 --}}
              <div class="form-group">
                <label for="password"
                  >パスワード<span class="required">必須</span></label
                >
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-control"
                  placeholder="8文字以上の半角英数字"
                  required
                  minlength="8"
                  autocomplete="new-password"
                />
                <p class="form-control-hint">8文字以上で設定してください</p>
                <p class="error-message" id="passwordError"></p>
              </div>

              {{-- パスワード確認入力欄 --}}
              <div class="form-group">
                <label for="passwordConfirm"
                  >パスワード（確認用）<span class="required">必須</span></label
                >
                <input
                  type="password"
                  id="passwordConfirm"
                  name="password_confirmation"
                  class="form-control"
                  placeholder="もう一度パスワードを入力してください"
                  required
                  minlength="8"
                  autocomplete="new-password"
                />
                <p class="error-message" id="passwordConfirmError"></p>
              </div>

              {{-- 電話番号入力欄 --}}
              <div class="form-group">
                <label for="phone"
                  >電話番号<span class="form-control-hint" style="margin-left: 8px;">任意</span></label
                >
                <input
                  type="tel"
                  id="phone"
                  name="tel"
                  class="form-control"
                  placeholder="例: 09012345678（ハイフンなし）"
                  pattern="[0-9]{10,11}"
                  autocomplete="tel"
                  value="{{ old('tel') }}"
                />
                <p class="form-control-hint">
                  10〜11桁の数字で入力してください
                </p>
                <p class="error-message" id="phoneError"></p>
              </div>

              {{-- 生年月日入力欄 --}}
              <div class="form-group">
                <label for="birthdate"
                  >生年月日<span class="form-control-hint" style="margin-left: 8px;">任意</span></label
                >
                <input
                  type="date"
                  id="birthdate"
                  name="birth"
                  class="form-control"
                  max=""
                  autocomplete="bday"
                  value="{{ old('birth') }}"
                />
                <p class="error-message" id="birthdateError"></p>
              </div>

              {{-- 性別選択（ラジオボタン） --}}
              <div class="form-group">
                <label>性別<span class="form-control-hint" style="margin-left: 8px;">任意</span></label>
                <div class="radio-group">
                  <label class="radio-label">
                    <input type="radio" name="gender" value="0" />
                    <span>男性</span>
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="gender" value="1" />
                    <span>女性</span>
                  </label>
                  <label class="radio-label">
                    <input type="radio" name="gender" value="2" />
                    <span>その他</span>
                  </label>
                </div>
                <p class="error-message" id="genderError"></p>
              </div>

              {{-- 本人確認書類アップロード欄 --}}
              <div class="form-group">
                <label for="identification"
                  >本人確認書類<span class="required">必須</span></label
                >
                <input
                  type="file"
                  id="identification"
                  name="identification"
                  class="form-control"
                  required
                  accept=".jpg,.jpeg,.png,.heic"
                />
                <p class="form-control-hint">
                  jpeg, jpg, png, heic形式の画像（最大5MB）
                </p>
                <p class="error-message" id="identificationError"></p>
              </div>

              {{-- 送信ボタン --}}
              <button type="submit" class="submit-btn">登録する</button>
            </form>

            {{-- ログインページへのリンク --}}
            <div class="login-link">
              すでにアカウントをお持ちですか？
              <a href="{{ route('login') }}">ログインはこちら</a>
            </div>
          </div>
        </div>
      </section>
    </main>

    {{-- ============================================================
         JavaScriptバリデーション
         ============================================================
         
         【役割】
         フォーム送信前にクライアント側で入力値をチェックし、
         エラーがあればユーザーに即座にフィードバックします。
         
         【注意】
         クライアント側のバリデーションは補助的なものです。
         セキュリティ上、サーバー側（AuthController）でも
         必ずバリデーションを行います。
    --}}
    <script>
      // ============================================================
      // 生年月日の最大値を今日に設定（未来の日付を選択不可に）
      // ============================================================
      const today = new Date().toISOString().split("T")[0];
      document.getElementById("birthdate").setAttribute("max", today);

      // ============================================================
      // DOM要素の取得
      // ============================================================
      const form = document.getElementById("registrationForm");
      
      // エラーメッセージ要素を連想配列で管理
      const errorMessages = {
        loginId: document.getElementById("loginIdError"),
        email: document.getElementById("emailError"),
        password: document.getElementById("passwordError"),
        passwordConfirm: document.getElementById("passwordConfirmError"),
        phone: document.getElementById("phoneError"),
        birthdate: document.getElementById("birthdateError"),
        gender: document.getElementById("genderError"),
        identification: document.getElementById("identificationError"),
      };

      // ============================================================
      // エラー表示関数
      // ============================================================
      /**
       * 指定フィールドにエラーを表示
       * @param {string} field - フィールド名
       * @param {string} message - エラーメッセージ
       */
      function showError(field, message) {
        // 入力要素を取得（IDまたはname属性で検索）
        const input =
          document.getElementById(field) ||
          document.querySelector(`[name="${field}"]`);
        const errorElement = errorMessages[field];

        // エラースタイルを適用
        if (input) input.classList.add("error");
        if (errorElement) {
          errorElement.textContent = message;
          errorElement.classList.add("show");
        }
      }

      // ============================================================
      // エラークリア関数
      // ============================================================
      /**
       * 指定フィールドのエラーをクリア
       * @param {string} field - フィールド名
       */
      function clearError(field) {
        const input =
          document.getElementById(field) ||
          document.querySelector(`[name="${field}"]`);
        const errorElement = errorMessages[field];

        // エラースタイルを除去
        if (input) input.classList.remove("error");
        if (errorElement) {
          errorElement.textContent = "";
          errorElement.classList.remove("show");
        }
      }

      /**
       * すべてのエラーをクリア
       */
      function clearAllErrors() {
        Object.keys(errorMessages).forEach(clearError);
      }

      // ============================================================
      // バリデーション関数（メイン）
      // ============================================================
      /**
       * フォーム全体のバリデーションを実行
       * @returns {boolean} バリデーション成功ならtrue
       */
      function validateForm() {
        clearAllErrors();  // まず全エラーをクリア
        let isValid = true;

        // ============================================================
        // ログインIDのバリデーション
        // ============================================================
        const loginId = document.getElementById("loginId").value.trim();
        if (!loginId) {
          showError("loginId", "ログインIDを入力してください");
          isValid = false;
        } else if (!/^[a-zA-Z0-9_]{4,20}$/.test(loginId)) {
          // 正規表現: 4〜20文字の半角英数字とアンダースコアのみ
          showError("loginId", "4〜20文字の半角英数記号（_）で入力してください");
          isValid = false;
        }

        // ============================================================
        // メールアドレスのバリデーション
        // ============================================================
        const email = document.getElementById("email").value.trim();
        if (!email) {
          showError("email", "メールアドレスを入力してください");
          isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          // 正規表現: 簡易的なメール形式チェック
          showError("email", "正しいメールアドレス形式で入力してください");
          isValid = false;
        }

        // ============================================================
        // パスワードのバリデーション（8〜20文字、英数混合必須）
        // ============================================================
        const password = document.getElementById("password").value;
        if (!password) {
          showError("password", "パスワードを入力してください");
          isValid = false;
        } else if (password.length < 8 || password.length > 20) {
          showError("password", "パスワードは8〜20文字で入力してください");
          isValid = false;
        } else if (!/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$/.test(password)) {
          // 正規表現: 英字と数字の両方を含む
          // (?=.*[a-zA-Z]) → 英字が最低1文字
          // (?=.*[0-9]) → 数字が最低1文字
          showError("password", "パスワードは英字と数字の両方を含めてください");
          isValid = false;
        }

        // ============================================================
        // パスワード確認のバリデーション
        // ============================================================
        const passwordConfirm = document.getElementById("passwordConfirm").value;
        if (!passwordConfirm) {
          showError("passwordConfirm", "パスワード（確認用）を入力してください");
          isValid = false;
        } else if (password !== passwordConfirm) {
          showError("passwordConfirm", "パスワードが一致しません");
          isValid = false;
        }

        // ============================================================
        // 電話番号のバリデーション（任意、入力時のみチェック）
        // ============================================================
        const phone = document.getElementById("phone").value.trim();
        if (phone && !/^[0-9]{10,11}$/.test(phone)) {
          // 正規表現: 10〜11桁の数字のみ
          showError("phone", "10〜11桁の数字で入力してください（ハイフンなし）");
          isValid = false;
        }

        // 生年月日・性別は任意のためバリデーション不要

        // ============================================================
        // 本人確認書類のバリデーション
        // ============================================================
        const identification = document.getElementById("identification");
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'heic'];  // 許可する拡張子
        
        if (!identification.files || identification.files.length === 0) {
          showError("identification", "本人確認書類をアップロードしてください");
          isValid = false;
        } else {
          const file = identification.files[0];
          const maxSize = 5 * 1024 * 1024;  // 5MB（バイト単位）
          const fileName = file.name.toLowerCase();
          const extension = fileName.split('.').pop();  // 拡張子を取得
          
          // 拡張子チェック
          if (!allowedExtensions.includes(extension)) {
            showError("identification", "jpeg, jpg, png, heic形式のみアップロード可能です");
            isValid = false;
          } else if (file.size > maxSize) {
            // ファイルサイズチェック
            showError("identification", "ファイルサイズは5MB以下にしてください");
            isValid = false;
          }
        }

        return isValid;
      }

      // ============================================================
      // リアルタイムバリデーション（入力フィールドからフォーカスが外れた時）
      // ============================================================
      ["loginId", "email", "password", "passwordConfirm", "phone", "birthdate"].forEach((id) => {
        const input = document.getElementById(id);
        if (input) {
          input.addEventListener("blur", () => {
            // 値が入力されていればエラーをクリア
            if (input.value) clearError(id);
          });
        }
      });

      // ============================================================
      // フォーム送信処理
      // ============================================================
      form.addEventListener("submit", function (e) {
        // デフォルトの送信動作を一旦停止
        e.preventDefault();

        // バリデーション実行
        if (validateForm()) {
          // 送信ボタンを無効化（二重送信防止）
          const submitBtn = form.querySelector(".submit-btn");
          submitBtn.disabled = true;
          submitBtn.textContent = "登録中...";

          // フォームを実際に送信（Laravelのルートへ）
          this.submit();
        }
      });
    </script>
@endsection