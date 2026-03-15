{{--
============================================================
レンタル確認画面 (rental_confirm.blade.php)
============================================================

【対応画面定義】
- booking_confirmation_screen.html

【このファイルの役割】
- 土地のレンタル確認情報を表示
- 予約内容の確認
- 予約の確定

【受け取るデータ】
- $land: 土地の詳細情報（オーナー含む）
- $time_start: 利用開始日時
- $time_end: 利用終了日時
- $total_price: 合計金額
- $prefectures: 都道府県の一覧

============================================================
--}}

@extends('layouts.app')

@section('title', 'レンタル確認')

@section('content')
    {{-- パンくずリスト --}}
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb-list">
            <a href="{{ route('home') }}" class="breadcrumb-item">トップ</a>
            <span class="breadcrumb-separator">›</span>
            <a href="{{ route('search') }}" class="breadcrumb-item">検索結果</a>
            <span class="breadcrumb-separator">›</span>
            <a href="{{ route('land.detail', $land->LAND_ID) }}" class="breadcrumb-item">
                {{ $land->NAME ?? $land->CITY . $land->STREET_ADDRESS }}
            </a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">レンタル確認</span>
        </nav>
    </div>

    <div class="confirm-section">
        {{-- 戻るリンク --}}
        <a href="{{ route('land.detail', $land->LAND_ID) }}" class="back-link">
            ← 土地詳細に戻る
        </a>

        {{-- ページタイトル --}}
        <h1 class="page-title">レンタル確認</h1>

        {{-- バリデーションエラー --}}
        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- メインレイアウト --}}
        <div class="confirm-layout">
            {{-- 左側：土地情報 --}}
            <div class="confirm-main">
                {{-- 土地情報カード --}}
                <div class="info-card">
                    <h2 class="info-card-title">土地情報</h2>
                    <div class="land-summary">
                        <div class="land-image">
                            @if($land->IMAGE)
                                <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="{{ $land->NAME ?? '土地' }}">
                            @else
                                <span>土地の写真</span>
                            @endif
                        </div>
                        <div class="land-details">
                            <h3 class="land-name">{{ $land->NAME ?? $land->CITY . $land->STREET_ADDRESS }}</h3>
                            <p class="land-location">
                                @php
                                    $prefName = $prefectures[$land->PEREFECTURES] ?? '';
                                @endphp
                                {{ $prefName }}{{ $land->CITY }}{{ $land->STREET_ADDRESS }}
                            </p>
                            <p class="land-area">面積: {{ number_format($land->AREA, 2) }}㎡</p>
                        </div>
                    </div>
                </div>

                {{-- 貸し手情報カード --}}
                <div class="info-card">
                    <h2 class="info-card-title">貸し手情報</h2>
                    <div class="owner-info">
                        <div class="owner-avatar">
                            @if($land->owner && $land->owner->ICON_IMAGE && $land->owner->ICON_IMAGE !== 'default_icon.png')
                                <img src="{{ asset('storage/' . $land->owner->ICON_IMAGE) }}" alt="オーナー">
                            @else
                                👤
                            @endif
                        </div>
                        <div class="owner-details">
                            <div class="owner-name">{{ $land->owner->USERNAME ?? '不明' }}</div>
                            <div class="owner-label">オーナー</div>
                        </div>
                    </div>
                </div>

                {{-- 注意事項 --}}
                <div class="info-card">
                    <h2 class="info-card-title">ご利用にあたって</h2>
                    <div class="notice-text">
                        <ul>
                            <li>予約確定後、貸し手との連絡が可能になります。</li>
                            <li>利用開始日時までに利用方法の確認を行ってください。</li>
                            <li>キャンセルは利用開始24時間前まで可能です。</li>
                            <li>ルール違反があった場合、強制退去となる場合があります。</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 右側：予約内容・確定 --}}
            <div class="confirm-sidebar">
                <div class="info-card">
                    <h2 class="info-card-title">ご予約内容</h2>

                    {{-- 土地サムネイル --}}
                    <div class="summary-image">
                        @if($land->IMAGE)
                            <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="{{ $land->NAME ?? '土地' }}">
                        @else
                            <span>土地の写真</span>
                        @endif
                    </div>
                    <h3 class="summary-title">{{ $land->NAME ?? $land->CITY . $land->STREET_ADDRESS }}</h3>

                    {{-- 利用期間 --}}
                    <div class="info-row">
                        <span class="info-label">利用開始</span>
                        <span class="info-value">
                            @if($time_start)
                                {{ \Carbon\Carbon::parse($time_start)->format('Y/m/d H:i') }}
                            @else
                                未設定
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">利用終了</span>
                        <span class="info-value">
                            @if($time_end)
                                {{ \Carbon\Carbon::parse($time_end)->format('Y/m/d H:i') }}
                            @else
                                未設定
                            @endif
                        </span>
                    </div>

                    {{-- 料金内訳 --}}
                    <div class="price-breakdown">
                        <div class="info-row">
                            <span class="info-label">
                                @php
                                    $priceUnitLabel = match ($land->PRICE_UNIT) {
                                        0 => '日額利用料',
                                        1 => '時間利用料',
                                        2 => '15分利用料',
                                        default => '利用料'
                                    };
                                @endphp
                                {{ $priceUnitLabel }}
                            </span>
                            <span class="info-value">¥{{ number_format($land->PRICE) }}</span>
                        </div>
                    </div>

                    {{-- 合計金額 --}}
                    <div class="total-row">
                        <span class="total-label">合計金額</span>
                        <span class="total-value">¥{{ number_format($total_price) }}</span>
                    </div>

                    {{-- 予約確定フォーム --}}
                    <form action="{{ route('rental.store', $land->LAND_ID) }}" method="POST" class="confirm-form">
                        @csrf
                        <input type="hidden" name="time_start" value="{{ $time_start }}">
                        <input type="hidden" name="time_end" value="{{ $time_end }}">

                        <div class="action-buttons">
                            <button type="submit" class="btn btn-primary btn-large">
                                この内容で予約を確定する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* レンタル確認ページ専用スタイル */

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
        .confirm-section {
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
        .confirm-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
            margin-top: 20px;
        }

        .confirm-main {
            flex: 1;
        }

        .confirm-sidebar {
            position: sticky;
            top: 80px;
            height: fit-content;
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

        /* 土地サマリー */
        .land-summary {
            display: flex;
            gap: 16px;
        }

        .land-image {
            width: 120px;
            height: 90px;
            border-radius: 6px;
            background: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 12px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .land-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .land-details {
            flex: 1;
        }

        .land-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .land-location {
            font-size: 14px;
            color: var(--text-gray);
            margin-bottom: 4px;
        }

        .land-area {
            font-size: 14px;
            color: var(--text-gray);
        }

        /* オーナー情報 */
        .owner-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .owner-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 20px;
            overflow: hidden;
        }

        .owner-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .owner-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .owner-label {
            font-size: 12px;
            color: var(--text-gray);
        }

        /* 注意事項 */
        .notice-text {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.8;
        }

        .notice-text ul {
            margin: 0;
            padding-left: 20px;
        }

        .notice-text li {
            margin-bottom: 8px;
        }

        /* サマリー画像 */
        .summary-image {
            width: 100%;
            height: 120px;
            border-radius: 6px;
            background: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .summary-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .summary-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 16px;
        }

        /* 情報行 */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--bg-light);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: var(--text-gray);
            font-size: 14px;
        }

        .info-value {
            color: var(--text-dark);
            font-size: 14px;
            font-weight: 500;
            text-align: right;
        }

        /* 料金内訳 */
        .price-breakdown {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--bg-light);
        }

        /* 合計金額 */
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .total-label {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .total-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        /* アクションボタン */
        .action-buttons {
            margin-top: 24px;
        }

        .btn-large {
            padding: 12px 24px;
            font-size: 16px;
            width: 100%;
        }

        /* エラーアラート */
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        /* レスポンシブ */
        @media (max-width: 900px) {
            .page-title {
                font-size: 22px;
            }

            .confirm-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .confirm-sidebar {
                position: static;
            }

            .land-summary {
                flex-direction: column;
            }

            .land-image {
                width: 100%;
                height: 150px;
            }
        }
    </style>
@endpush