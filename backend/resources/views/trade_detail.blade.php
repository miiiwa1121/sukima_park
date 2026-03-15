@extends('layouts.app')

@section('title', '取引完了履歴詳細 - スキマパーク')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/trade-detail.css') }}">
@endpush

@section('content')
<div class="section">
    <div class="container">
        <a href="{{ route('rental.history') }}" class="back-link">
            ← 履歴一覧に戻る
        </a>

        <h1 class="page-title">取引完了履歴詳細</h1>

          {{-- 取引情報エリア --}}
          <div class="transaction-summary">
            <div class="transaction-image">
                @if($rental->land->main_image)
                    <img src="{{ Storage::url($rental->land->main_image) }}" alt="{{ $rental->land->name }}">
                @else
                    土地の写真
                @endif
            </div>
            <div class="transaction-info">
              <h2 class="transaction-title">{{ $rental->land->name }}</h2>
              <div class="info-row">
                <span class="info-label">貸し手</span>
                <span class="info-value">{{ $rental->land->owner->name }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">利用期間</span>
                <span class="info-value">
                    {{ $rental->start_date->format('Y/m/d') }} 〜 {{ $rental->end_date->format('Y/m/d') }}
                </span>
              </div>
              <div class="info-row">
                <span class="info-label">所在地</span>
                <span class="info-value">{{ $rental->land->full_address }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">支払い金額</span>
                <span class="info-value">¥{{ number_format($rental->total_amount) }}</span>
              </div>
              <span class="completed-badge">{{ $rental->status_label }}</span>
            </div>
          </div>

        {{-- レビューエリア --}}
        @if($reviews->isNotEmpty())
            <div class="review-section">
                <h2 class="review-section-title">あなたが投稿したレビュー</h2>

                @foreach($reviews as $index => $review)
                    @if($index > 0)
                        <div class="divider"></div>
                    @endif

                    <div class="review-display">
                        <div class="review-header">
                            <span class="review-target-name">
                                @if($review->reviewable_type === 'land')
                                    🏠 土地へのレビュー
                                @else
                                    👤 貸し手（{{ $rental->land->owner->name }}さん）へのレビュー
                                @endif
                            </span>
                            <span class="review-stars">
                                {!! str_repeat('★', $review->rating) !!}{!! str_repeat('☆', 5 - $review->rating) !!}
                            </span>
                        </div>
                        <div class="review-date">投稿日: {{ $review->created_at->format('Y/m/d') }}</div>
                        <div class="review-comment">{{ $review->comment }}</div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- レビュー未投稿の場合 --}}
            <div class="review-section">
                <h2 class="review-section-title">レビューを投稿する</h2>
                <p style="color: #666; font-size: 14px; line-height: 1.6;">
                    この取引についてのレビューを投稿できます。<br>
                    土地とオーナーについてのご意見をお聞かせください。
                </p>
                <a href="{{ route('review.create', $rental->RECORD_ID) }}" class="submit-btn" style="display: inline-block; margin-top: 16px;">
                    レビューを投稿する
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
