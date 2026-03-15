{{--
============================================================
土地登録画面 (land_register.blade.php)
============================================================

【このビューの役割】
土地登録用のフォーム画面を表示します。

【入力項目】
- 都道府県（必須）: セレクトボックス
- 市区町村（必須）
- 住所（必須）
- 面積（必須、数値）
- 料金（任意）
- 説明（任意）
- 画像（任意）

【URLとルート】
- 表示: GET /land/register (ルート名: land.register)
- 送信: POST /land/register (LandController@register)

============================================================
--}}

@extends('layouts.app')

@section('title', '土地を登録')

@section('content')

@php
    // セッションから登録データを取得（確認画面から戻った場合用）
    $sessionData = session('land_register', []);
@endphp

{{-- ページ固有のスタイル --}}
<style>
    :root {
        --primary-color: #2e7d32;
        --primary-hover: #1b5e20;
        --error-color: #d32f2f;
        --border-color: #e0e0e0;
        --text-primary: #333;
        --text-secondary: #555;
        --text-hint: #888;
        --bg-page: #fafafa;
        --bg-white: #fff;
        --bg-secondary: #f5f5f5;
        --bg-hover: #e0e0e0;
        --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        --border-radius: 6px;
        --transition: all 0.2s;
    }

    .registration-section {
        padding: 40px 0;
    }

    .form-container {
        background: var(--bg-white);
        border-radius: 8px;
        padding: 32px;
        box-shadow: var(--shadow);
        max-width: 700px;
        margin: 0 auto;
    }

    .form-container h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #222;
        text-align: center;
    }

    .form-subtitle {
        color: var(--text-hint);
        font-size: 14px;
        margin-bottom: 32px;
        text-align: center;
    }

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

    .required {
        color: var(--error-color);
        font-size: 12px;
        margin-left: 4px;
    }

    .optional {
        color: var(--text-hint);
        font-size: 12px;
        margin-left: 4px;
        font-weight: normal;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }

    .form-control.error {
        border-color: var(--error-color);
    }

    .form-control[type="file"] {
        padding: 8px 12px;
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .form-control-hint {
        font-size: 12px;
        color: var(--text-hint);
        margin-top: 4px;
    }

    .error-message {
        font-size: 12px;
        color: var(--error-color);
        margin-top: 4px;
    }

    /* 入力グループ（単位付き） */
    .input-group {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .input-group .form-control {
        flex: 1;
    }

    .input-group-text {
        color: var(--text-secondary);
        font-size: 14px;
        white-space: nowrap;
    }

    /* 送信ボタン */
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

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* 成功メッセージ */
    .success-message {
        background: #e8f5e9;
        color: var(--primary-color);
        padding: 12px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
        text-align: center;
    }

    /* エラーメッセージ（サーバー側） */
    .error-box {
        background: #ffebee;
        color: #c62828;
        padding: 12px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
    }

    .error-box ul {
        margin: 8px 0 0 20px;
    }

    /* ファイルプレビュー */
    .file-preview {
        margin-top: 12px;
    }

    .file-preview img {
        max-width: 200px;
        max-height: 150px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
    }

    /* レスポンシブ */
    @media (max-width: 768px) {
        .form-container {
            padding: 24px 16px;
        }

        .form-container h1 {
            font-size: 20px;
        }
    }
</style>

<main>
    <section class="registration-section">
        <div class="container">
            <div class="form-container">
                <h1>土地を登録</h1>
                <p class="form-subtitle">あなたの土地情報を入力してください</p>

                {{-- 成功メッセージ --}}
                @if (session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
                @endif

                {{-- サーバー側バリデーションエラー表示 --}}
                @if ($errors->any())
                <div class="error-box">
                    <strong>エラーが発生しました：</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{--
                  土地登録フォーム
                  - method="POST": データをPOSTで送信
                  - action: ルート名'land.register'に送信
                  - enctype="multipart/form-data": ファイルアップロードに必要
                --}}
                <form id="landRegistrationForm" method="POST" action="{{ route('land.register') }}" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- 土地名 --}}
                    <div class="form-group">
                        <label for="name">土地名<span class="required">必須</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') error @enderror"
                            placeholder="例: 駅前スペース"
                            required
                            maxlength="255"
                            value="{{ old('name', $sessionData['name'] ?? '') }}"
                        />
                        <p class="form-control-hint">借り手に分かりやすい名前を付けてください</p>
                        @error('name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 都道府県 --}}
                    <div class="form-group">
                        <label for="prefectures">都道府県<span class="required">必須</span></label>
                        <select
                            id="prefectures"
                            name="prefectures"
                            class="form-control @error('prefectures') error @enderror"
                            required
                        >
                            <option value="">選択してください</option>
                            @php
                                $prefectures = [
                                    1 => '北海道', 2 => '青森県', 3 => '岩手県', 4 => '宮城県', 5 => '秋田県', 6 => '山形県', 7 => '福島県',
                                    8 => '茨城県', 9 => '栃木県', 10 => '群馬県', 11 => '埼玉県', 12 => '千葉県', 13 => '東京都', 14 => '神奈川県',
                                    15 => '新潟県', 16 => '富山県', 17 => '石川県', 18 => '福井県', 19 => '山梨県', 20 => '長野県',
                                    21 => '岐阜県', 22 => '静岡県', 23 => '愛知県', 24 => '三重県',
                                    25 => '滋賀県', 26 => '京都府', 27 => '大阪府', 28 => '兵庫県', 29 => '奈良県', 30 => '和歌山県',
                                    31 => '鳥取県', 32 => '島根県', 33 => '岡山県', 34 => '広島県', 35 => '山口県',
                                    36 => '徳島県', 37 => '香川県', 38 => '愛媛県', 39 => '高知県',
                                    40 => '福岡県', 41 => '佐賀県', 42 => '長崎県', 43 => '熊本県', 44 => '大分県', 45 => '宮崎県', 46 => '鹿児島県', 47 => '沖縄県'
                                ];
                            @endphp
                            @foreach ($prefectures as $code => $name)
                                <option value="{{ $code }}" {{ old('prefectures', $sessionData['prefectures'] ?? '') == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('prefectures')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 市区町村 --}}
                    <div class="form-group">
                        <label for="city">市区町村<span class="required">必須</span></label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            class="form-control @error('city') error @enderror"
                            placeholder="例: 渋谷区"
                            required
                            maxlength="255"
                            value="{{ old('city', $sessionData['city'] ?? '') }}"
                        />
                        @error('city')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 住所（番地） --}}
                    <div class="form-group">
                        <label for="street_address">住所（番地）<span class="required">必須</span></label>
                        <input
                            type="text"
                            id="street_address"
                            name="street_address"
                            class="form-control @error('street_address') error @enderror"
                            placeholder="例: 渋谷1-2-3"
                            required
                            maxlength="255"
                            value="{{ old('street_address', $sessionData['street_address'] ?? '') }}"
                        />
                        <p class="form-control-hint">正確な住所を入力してください</p>
                        @error('street_address')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 面積 --}}
                    <div class="form-group">
                        <label for="area">面積<span class="required">必須</span></label>
                        <div class="input-group">
                            <input
                                type="number"
                                id="area"
                                name="area"
                                class="form-control @error('area') error @enderror"
                                placeholder="例: 50"
                                required
                                min="0.1"
                                step="0.01"
                                value="{{ old('area', $sessionData['area'] ?? '') }}"
                            />
                            <span class="input-group-text">㎡</span>
                        </div>
                        @error('area')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 料金 --}}
                    <div class="form-group">
                        <label for="price">料金<span class="optional">（任意）</span></label>
                        <div class="input-group">
                            <input
                                type="number"
                                id="price"
                                name="price"
                                class="form-control @error('price') error @enderror"
                                placeholder="例: 3000"
                                min="0"
                                value="{{ old('price', $sessionData['price'] ?? '') }}"
                            />
                            <span class="input-group-text">円</span>
                        </div>
                        <p class="form-control-hint">後から設定することもできます</p>
                        @error('price')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 説明 --}}
                    <div class="form-group">
                        <label for="description">説明<span class="optional">（任意）</span></label>
                        <textarea
                            id="description"
                            name="description"
                            class="form-control @error('description') error @enderror"
                            placeholder="土地の特徴、利用可能な用途、注意事項などを詳しく記載してください"
                            maxlength="1000"
                        >{{ old('description', $sessionData['description'] ?? '') }}</textarea>
                        <p class="form-control-hint">詳細な説明は借り手の判断材料になります（最大1000文字）</p>
                        @error('description')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 土地の権利書 --}}
                    <div class="form-group">
                        <label for="title_deed">土地の権利書<span class="required">必須</span></label>
                        <input
                            type="file"
                            id="title_deed"
                            name="title_deed"
                            class="form-control @error('title_deed') error @enderror"
                            accept="image/*"
                            required
                        />
                        <p class="form-control-hint">登記簿謄本、権利証などの書類をアップロードしてください（最大5MB）</p>
                        @error('title_deed')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 土地の画像 --}}
                    <div class="form-group">
                        <label for="image">土地の画像<span class="required">必須</span></label>
                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control @error('image') error @enderror"
                            accept="image/*"
                            required
                        />
                        <p class="form-control-hint">土地の様子が分かる写真をアップロードしてください（最大5MB）</p>
                        @error('image')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 送信ボタン --}}
                    <button type="submit" class="submit-btn" id="submitBtn">登録する</button>
                </form>
            </div>
        </div>
    </section>
</main>

{{-- フォーム送信時にボタンを無効化 --}}
<script>
    document.getElementById('landRegistrationForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = '確認中...';
    });
</script>

@endsection