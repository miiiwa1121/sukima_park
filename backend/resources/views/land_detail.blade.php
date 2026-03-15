{{--
============================================================
土地詳細画面 (land_detail.blade.php)
============================================================

【対応画面定義】
- land_detail_screen.html

【このファイルの役割】
- 土地の詳細情報を表示
- ギャラリー（写真表示）
- 土地情報・詳細説明・利用ルール
- オーナー情報
- レビュー一覧
- 予約フォーム（利用開始日時・終了日時）

【受け取るデータ】
- $land: 土地の詳細情報（オーナー、レビュー含む）
- $prefectures: 都道府県の一覧

============================================================
--}}

@extends('layouts.app')

@section('title', ($land->NAME ?? $land->CITY . $land->STREET_ADDRESS) . ' - 土地詳細')

@section('content')
    {{-- パンくずリスト --}}
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb-list">
            <a href="{{ route('home') }}" class="breadcrumb-item">トップ</a>
            <span class="breadcrumb-separator">›</span>
            <a href="{{ route('lands.index') }}" class="breadcrumb-item">検索結果</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">{{ $land->NAME ?? $land->CITY . $land->STREET_ADDRESS }}</span>
        </nav>
    </div>

    <div class="detail-section">
        {{-- 戻るリンク --}}
        <a href="{{ route('lands.index') }}" class="back-link">
            ← 検索結果に戻る
        </a>

        {{-- ページタイトル --}}
        <h1 class="page-title">{{ $land->NAME ?? $land->CITY . $land->STREET_ADDRESS }}</h1>

        {{-- メインレイアウト --}}
        <div class="detail-layout">
            {{-- 左側：メインコンテンツ --}}
            <div class="detail-main">
                {{-- ギャラリー --}}
                <div class="gallery">
                    <div class="gallery-main">
                        @if($land->IMAGE)
                            <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="{{ $land->NAME ?? '土地' }}" id="main-image">
                        @else
                            <span>メイン写真</span>
                        @endif
                    </div>
                    <div class="gallery-thumbs">
                        @if($land->IMAGE)
                            <div class="gallery-thumb active"
                                onclick="changeMainImage('{{ asset('storage/' . $land->IMAGE) }}')">
                                <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="写真1">
                            </div>
                        @endif
                        {{-- 追加のサムネイル画像がある場合はここに表示 --}}
                        <div class="gallery-thumb">写真2</div>
                        <div class="gallery-thumb">写真3</div>
                        <div class="gallery-thumb">写真4</div>
                    </div>
                </div>

                {{-- 土地情報 --}}
                <div class="info-card">
                    <h2 class="info-card-title">土地情報</h2>
                    <div class="info-row">
                        <span class="info-label">所在地</span>
                        <span class="info-value">
                            @php
                                $prefName = $prefectures[$land->PEREFECTURES] ?? '';
                            @endphp
                            {{ $prefName }}{{ $land->CITY }}{{ $land->STREET_ADDRESS }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">面積</span>
                        <span class="info-value">{{ number_format($land->AREA, 2) }}㎡</span>
                    </div>
                    @if($land->NAME)
                        <div class="info-row">
                            <span class="info-label">用途</span>
                            <span class="info-value">{{ $land->NAME }}</span>
                        </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">利用可能時間</span>
                        <span class="info-value">
                            @if($land->RENTAL_START_TIME && $land->RENTAL_END_TIME)
                                {{ \Carbon\Carbon::parse($land->RENTAL_START_TIME)->format('H:i') }}
                                〜
                                {{ \Carbon\Carbon::parse($land->RENTAL_END_TIME)->format('H:i') }}
                            @else
                                24時間利用可能
                            @endif
                        </span>
                    </div>
                    @if($land->RENTAL_START_DATE && $land->RENTAL_END_DATE)
                        <div class="info-row">
                            <span class="info-label">貸出期間</span>
                            <span class="info-value rental-period-value">
                                {{ $land->RENTAL_START_DATE->format('Y/m/d') }}
                                〜
                                {{ $land->RENTAL_END_DATE->format('Y/m/d') }}
                            </span>
                        </div>
                    @endif

                    {{-- Google Map埋め込みエリア --}}
                    <div class="map-container">
                        [Google Map埋め込みエリア]
                    </div>
                </div>

                {{-- 詳細説明 --}}
                @if($land->DESCRIPTION)
                    <div class="info-card">
                        <h2 class="info-card-title">詳細説明</h2>
                        <div class="description-text">{{ $land->DESCRIPTION }}</div>
                    </div>
                @endif

                {{-- 利用ルール --}}
                <div class="info-card">
                    <h2 class="info-card-title">利用ルール</h2>
                    <div class="description-text">
                        1. 貸し出しスペースは写真の範囲内のみです
                        2. 他の利用者の迷惑にならないようご配慮ください
                        3. ゴミは各自お持ち帰りください
                        4. 火気の使用は厳禁です
                        5. 騒音にご注意ください
                    </div>
                </div>
            </div>

            {{-- 右側：サイドバー --}}
            <div class="detail-sidebar">
                {{-- 料金・予約カード --}}
                <div class="info-card">
                    <div class="price-label">
                        @php
                            $priceUnitLabel = match ($land->PRICE_UNIT) {
                                0 => '日額利用料',
                                1 => '時間利用料',
                                2 => '15分利用料',
                                default => '利用料'
                            };
                        @endphp
                        {{ $priceUnitLabel }}
                    </div>
                    <div class="price-value">
                        ¥{{ number_format($land->PRICE) }}
                    </div>

                    <form action="{{ route('rental.confirm', $land->LAND_ID) }}" method="GET" class="booking-form">
                        <div class="form-group">
                            <label for="time_start">利用開始日時</label>
                            <input type="datetime-local" id="time_start" name="time_start" class="form-control"
                                step="1800" />
                        </div>
                        <div class="form-group">
                            <label for="time_end">利用終了日時</label>
                            <input type="datetime-local" id="time_end" name="time_end" class="form-control" step="1800" />
                        </div>

                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary btn-large">
                                この土地を借りる
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 貸し手情報 --}}
                <div class="owner-card">
                    <h3 class="owner-card-title">貸し手情報</h3>
                    <div class="owner-header">
                        <div class="owner-avatar">
                            @if($land->owner && $land->owner->ICON_IMAGE && $land->owner->ICON_IMAGE !== 'default_icon.png')
                                <img src="{{ asset('storage/' . $land->owner->ICON_IMAGE) }}" alt="オーナーアイコン">
                            @else
                                👤
                            @endif
                        </div>
                        <div class="owner-info">
                            <div class="owner-name">{{ $land->owner->USERNAME ?? '不明' }}</div>
                            <div class="owner-label">オーナー</div>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('messages.show', $land->owner->USER_ID ?? 0) }}"
                            class="btn btn-primary btn-large">
                            💬 貸し手に連絡する
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- レビュー・コメント --}}
        <div class="review-section">
            <h2>レビュー・コメント</h2>
            @if($land->reviews && $land->reviews->count() > 0)
                @foreach($land->reviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-author">{{ $review->reviewer->USERNAME ?? '匿名' }}</span>
                            <span class="review-date">{{ $review->DATE ? $review->DATE->format('Y/m/d') : '' }}</span>
                        </div>
                        <div class="review-rating">
                            @php
                                $rating = $review->LAND_REVIEW ?? 0;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $rating)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </div>
                        <p class="review-text">{{ $review->LAND_COMMENT ?? '' }}</p>
                    </div>
                @endforeach
            @else
                <div class="no-reviews">
                    <p>まだレビューはありません。</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* 土地詳細ページ専用スタイル */

        /* パンくずリスト */
        .breadcrumb-wrapper {
            padding: 20px 0;
            background: var(--bg-white);
            border-bottom: 1px solid var(--border);
            margin: 0 -20px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .breadcrumb-list {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            flex-wrap: wrap;
        }

        .breadcrumb-item {
            color: var(--text-gray);
            text-decoration: none;
        }

        .breadcrumb-item:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .breadcrumb-separator {
            color: var(--text-light);
        }

        .breadcrumb-current {
            color: var(--text-dark);
            font-weight: 500;
        }

        /* 戻るリンク */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* セクション */
        .detail-section {
            padding: 40px 0;
        }

        /* ページタイトル */
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 20px;
        }

        /* レイアウト */
        .detail-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
            margin-top: 20px;
        }

        .detail-main {
            flex: 1;
        }

        .detail-sidebar {
            position: sticky;
            top: 80px;
            height: fit-content;
        }

        /* ギャラリー */
        .gallery {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .gallery-main {
            width: 100%;
            height: 400px;
            background: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 16px;
            overflow: hidden;
        }

        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-thumbs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding: 12px;
            background: var(--bg-light);
        }

        .gallery-thumb {
            height: 80px;
            background: var(--bg-gray);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 12px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s;
            overflow: hidden;
        }

        .gallery-thumb:hover,
        .gallery-thumb.active {
            border-color: var(--primary);
        }

        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* 情報カード */
        .info-card {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            padding: 24px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .info-card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid var(--bg-light);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: var(--text-gray);
            min-width: 120px;
            font-size: 14px;
        }

        .info-value {
            color: var(--text-dark);
            font-size: 14px;
            flex: 1;
        }

        .rental-period-value {
            color: var(--primary);
            font-weight: 600;
        }

        .description-text {
            color: var(--text-gray);
            font-size: 14px;
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .map-container {
            width: 100%;
            height: 300px;
            background: var(--bg-gray);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 16px;
            margin-top: 16px;
        }

        /* 料金表示 */
        .price-label {
            font-size: 14px;
            color: var(--text-gray);
            margin-bottom: 4px;
        }

        .price-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 24px;
        }

        /* フォーム */
        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--text-gray);
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-large {
            padding: 12px 24px;
            font-size: 16px;
            width: 100%;
        }

        /* オーナーカード */
        .owner-card {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            padding: 20px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .owner-card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-gray);
            margin-bottom: 12px;
        }

        .owner-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .owner-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 24px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .owner-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .owner-info {
            flex: 1;
        }

        .owner-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .owner-label {
            font-size: 12px;
            color: var(--text-gray);
        }

        /* レビューセクション */
        .review-section {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 24px;
            margin-top: 40px;
        }

        .review-section h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .review-item {
            border-bottom: 1px solid var(--bg-light);
            padding: 20px 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .review-author {
            font-weight: 600;
            color: var(--text-dark);
        }

        .review-date {
            color: var(--text-light);
            font-size: 13px;
        }

        .review-rating {
            color: #ffa726;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .review-text {
            color: var(--text-gray);
            line-height: 1.6;
        }

        .no-reviews {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-gray);
        }

        /* レスポンシブ */
        @media (max-width: 900px) {
            .page-title {
                font-size: 22px;
            }

            .detail-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .detail-sidebar {
                position: static;
            }

            .gallery-main {
                height: 250px;
            }

            .gallery-thumbs {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // サムネイルクリックでメイン画像を変更
        function changeMainImage(src) {
            const mainImage = document.getElementById('main-image');
            if (mainImage) {
                mainImage.src = src;
            }

            // サムネイルのアクティブ状態を更新
            document.querySelectorAll('.gallery-thumb').forEach(thumb => {
                thumb.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }

        // 予約フォームのバリデーション
        document.addEventListener('DOMContentLoaded', function () {
            const timeStart = document.getElementById('time_start');
            const timeEnd = document.getElementById('time_end');

            // 利用開始日時が変更された時の処理
            timeStart.addEventListener('change', function () {
                if (this.value) {
                    // 開始日時から15分後を計算
                    const startDate = new Date(this.value);
                    const minEndDate = new Date(startDate.getTime() + 15 * 60 * 1000); // 15分後

                    // 終了日時の最小値を設定
                    const minEndValue = formatDateTimeLocal(minEndDate);
                    timeEnd.min = minEndValue;

                    // 現在の終了日時が開始日時より15分以上後でない場合はクリア
                    if (timeEnd.value) {
                        const endDate = new Date(timeEnd.value);
                        if (endDate < minEndDate) {
                            timeEnd.value = '';
                            alert('利用終了日時は開始日時の15分後以降を選択してください。');
                        }
                    }
                } else {
                    // 開始日時がクリアされた場合、制限を解除
                    timeEnd.min = '';
                }
            });

            // 利用終了日時が変更された時の処理
            timeEnd.addEventListener('change', function () {
                if (this.value && timeStart.value) {
                    const startDate = new Date(timeStart.value);
                    const endDate = new Date(this.value);
                    const minEndDate = new Date(startDate.getTime() + 15 * 60 * 1000);

                    if (endDate < minEndDate) {
                        this.value = '';
                        alert('利用終了日時は開始日時の15分後以降を選択してください。');
                    }
                }
            });

            // Date オブジェクトを datetime-local 形式の文字列に変換
            function formatDateTimeLocal(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });
    </script>
@endpush