{{--
============================================================
土地登録確認画面 (land_register_confirm.blade.php)
============================================================

【このビューの役割】
土地登録内容の確認画面を表示します。

【URLとルート】
- 表示: GET /land/register/confirm (ルート名: land.register.confirm)
- 登録: POST /land/register/store (LandController@store)

============================================================
--}}

@extends('layouts.app')

@section('title', '土地登録確認')

@section('content')

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

    .confirmation-section {
        padding: 40px 0;
    }

    .confirmation-container {
        background: var(--bg-white);
        border-radius: 8px;
        padding: 32px;
        box-shadow: var(--shadow);
        max-width: 800px;
        margin: 0 auto;
    }

    .confirmation-container h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #222;
        text-align: center;
    }

    .confirmation-subtitle {
        color: var(--text-hint);
        font-size: 14px;
        margin-bottom: 32px;
        text-align: center;
    }

    .confirmation-section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 32px 0 20px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--primary-color);
    }

    .confirmation-section-title:first-of-type {
        margin-top: 0;
    }

    .confirmation-item {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .confirmation-item:last-child {
        border-bottom: none;
    }

    .confirmation-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-secondary);
    }

    .confirmation-value {
        font-size: 14px;
        color: var(--text-primary);
        word-break: break-word;
    }

    .confirmation-value.description {
        white-space: pre-wrap;
        line-height: 1.8;
    }

    .image-preview {
        max-width: 200px;
        max-height: 150px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
    }

    .btn {
        padding: 12px 24px;
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

    .btn-primary {
        background: var(--primary-color);
        color: var(--bg-white);
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--primary-hover);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: var(--bg-white);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-secondary);
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 32px;
        border-top: 1px solid var(--border-color);
    }

    .button-group .btn {
        flex: 1;
    }

    @media (max-width: 768px) {
        .confirmation-container {
            padding: 24px 16px;
        }

        .confirmation-container h1 {
            font-size: 20px;
        }

        .confirmation-item {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .button-group {
            flex-direction: column-reverse;
        }
    }
</style>

<main>
    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-container">
                <h1>登録内容の確認</h1>
                <p class="confirmation-subtitle">以下の内容で登録します。内容をご確認ください。</p>

                <!-- 基本情報 -->
                <h2 class="confirmation-section-title">基本情報</h2>

                <div class="confirmation-item">
                    <div class="confirmation-label">土地名</div>
                    <div class="confirmation-value">{{ $land['name'] }}</div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">都道府県</div>
                    <div class="confirmation-value">
                        @php
                            $prefectureNames = [
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
                        {{ $prefectureNames[$land['prefectures']] ?? '不明' }}
                    </div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">市区町村</div>
                    <div class="confirmation-value">{{ $land['city'] }}</div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">住所（番地）</div>
                    <div class="confirmation-value">{{ $land['street_address'] }}</div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">面積</div>
                    <div class="confirmation-value">{{ $land['area'] }} ㎡</div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">料金</div>
                    <div class="confirmation-value">
                        {{ $land['price'] ? number_format($land['price']) . ' 円' : '未設定' }}
                    </div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">説明</div>
                    <div class="confirmation-value description">
                        {{ $land['description'] ?: '未入力' }}
                    </div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">土地の権利書</div>
                    <div class="confirmation-value">
                        @if($land['title_deed_path'])
                            <img src="{{ asset('storage/' . $land['title_deed_path']) }}" alt="土地の権利書" class="image-preview">
                        @else
                            未アップロード
                        @endif
                    </div>
                </div>

                <div class="confirmation-item">
                    <div class="confirmation-label">土地の画像</div>
                    <div class="confirmation-value">
                        @if($land['image_path'])
                            <img src="{{ asset('storage/' . $land['image_path']) }}" alt="土地の画像" class="image-preview">
                        @else
                            未アップロード
                        @endif
                    </div>
                </div>

                <!-- ボタングループ -->
                <div class="button-group">
                    <a href="{{ route('land.register') }}" class="btn btn-secondary">修正する</a>
                    <form action="{{ route('land.register.store') }}" method="POST" style="flex: 1;">
                        @csrf
                        <button type="submit" class="btn btn-primary" id="submitBtn" style="width: 100%;">登録する</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

{{-- 送信ボタン無効化 --}}
<script>
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = '登録中...';
    });
</script>

@endsection