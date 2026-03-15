{{--
============================================================
レンタル詳細画面 (rental_detail.blade.php)
============================================================

【対応画面定義】
  - rental_detail.csv（レンタル詳細）

【このファイルの役割】
  - レンタル記録の詳細情報を表示
  - レビュー情報の表示

【受け取るデータ】
  - $rental: レンタル記録の詳細
    → RentalController@show から渡される
    → RentalRecord モデル（landリレーション、reviewリレーション含む）

【表示内容】
  1. 取引情報（土地情報、利用期間、支払金額など）
  2. レビューセクション

============================================================
--}}

@extends('layouts.app')

@section('title', 'レンタル詳細 - スキマパーク')

@section('content')
<div class="page-header">
    <div class="header-content">
        @php
            $fallbackBackUrl = url()->previous() ?: route('home');
            $backUrl = isset($backRoute)
                ? route($backRoute)
                : $fallbackBackUrl;
        @endphp
        <a href="{{ $backUrl }}" class="back-link">← 一覧に戻る</a>
        <h1 class="page-title">レンタル詳細</h1>
    </div>
</div>

<section class="section">
    <div class="container">
        {{-- 取引情報 --}}
        <div class="transaction-card">
            <div class="transaction-image">
                @if($rental->land && $rental->land->IMAGE)
                    <img src="{{ asset('storage/' . $rental->land->IMAGE) }}" alt="{{ $rental->land->CITY }} {{ $rental->land->STREET_ADDRESS }}">
                @else
                    <div class="placeholder-image">📍 画像なし</div>
                @endif
            </div>
            <div class="transaction-info">
                <h2 class="transaction-title">
                    {{ $rental->land->CITY ?? '不明' }} {{ $rental->land->STREET_ADDRESS ?? '' }}
                </h2>
                <div class="info-group">
                    <div class="info-row">
                        <span class="info-label">面積:</span>
                        <span class="info-value">{{ $rental->land->AREA ?? '不明' }}m²</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">レンタル期間:</span>
                        <span class="info-value">
                            {{ $rental->RENTAL_START_DATE->format('Y年m月d日') }}
                            ～
                            {{ $rental->RENTAL_END_DATE->format('Y年m月d日') }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">利用時間:</span>
                        <span class="info-value">
                            {{ $rental->RENTAL_START_TIME->format('H:i') }}
                            ～
                            {{ $rental->RENTAL_END_TIME->format('H:i') }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">単価:</span>
                        <span class="info-value">
                            ¥{{ number_format($rental->PRICE) }}/{{ ['日', '時間', '15分'][$rental->PRICE_UNIT] ?? '日' }}
                        </span>
                    </div>
                </div>
                <div class="status-badge completed">取引完了</div>
            </div>
        </div>

        {{-- レビューセクション --}}
        @if($rental->review)
        <div class="review-section">
            <h2 class="section-title">レビュー</h2>
            <div class="review-item">
                <div class="review-header">
                    <span class="review-label">このレンタルへのレビュー</span>
                    <span class="review-stars">
                        @for($i = 0; $i < 5; $i++)
                            @if($i < $rental->review->RATING)
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </span>
                </div>
                <div class="review-date">
                    投稿日:
                    @if($rental->review && $rental->review->DATE)
                        {{ $rental->review->DATE->format('Y年m月d日') }}
                    @else
                        -
                    @endif
                </div>
                <div class="review-comment">
                    {{ $rental->review->COMMENT }}
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/rental_detail.css') }}">
@endpush
