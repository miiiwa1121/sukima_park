{{--
============================================================
取引完了カード コンポーネント (trade-card.blade.php)
============================================================

【このコンポーネントの役割】
  - 1つの取引完了記録を表示するカード

【受け取るプロパティ】
  - $trade: RentalRecordモデルインスタンス
    - RECORD_ID: 貸出記録ID
    - RENTAL_START_DATE: 利用開始日
    - RENTAL_END_DATE: 利用終了日
    - PRICE: 価格
    - PRICE_UNIT: 価格単位
    - LAND_ID: 土地ID
    - USER_ID: ユーザーID
    - land: リレーション（LAND_TABLEの土地情報）
      - NAME: 土地名
      - PEREFECTURES: 都道府県
      - CITY: 市区町村
      - IMAGE: 画像ファイルパス
    - review: リレーション（REVIEW_COMMENT_TABLEのレビュー情報、存在しないこともある）
      - LAND_REVIEW: 土地への評価
      - LAND_COMMENT: 土地へのコメント

============================================================
--}}

<div class="rental-card">
    <div class="rental-card-image">
        @if($trade->land && $trade->land->IMAGE)
            <img src="{{ asset('storage/' . $trade->land->IMAGE) }}" alt="{{ $trade->land->NAME }}">
        @else
            <div style="background: #f0f0f0; height: 200px; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                📍
            </div>
        @endif
    </div>
    <div class="rental-card-body">
        <h3 class="rental-card-title">
            {{ $trade->land->CITY ?? '不明' }} {{ $trade->land->STREET_ADDRESS ?? '' }}
        </h3>
        <div class="rental-card-info">
            <div class="info-row">
                <span class="info-label">利用期間:</span>
                <span class="info-value rental-period">
                    {{ $trade->RENTAL_START_DATE->format('Y年m月d日') }}
                    ～
                    {{ $trade->RENTAL_END_DATE->format('Y年m月d日') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">支払い金額:</span>
                <span class="info-value">¥{{ number_format($trade->PRICE) }}</span>
            </div>
            @if($trade->review)
                <div class="info-row">
                    <span class="info-label">ステータス:</span>
                    <span class="info-value" style="color: #2e7d32; font-weight: 500;">✓ レビュー投稿済み</span>
                </div>
            @else
                <div class="info-row">
                    <span class="info-label">ステータス:</span>
                    <span class="info-value" style="color: #f57c00; font-weight: 500;">未レビュー</span>
                </div>
            @endif
        </div>
        <div class="rental-card-footer">
            @unless($trade->review)
                <a href="{{ route('review.create', $trade->RECORD_ID) }}" class="btn btn-secondary" style="margin-right: 8px;">レビューを投稿する</a>
            @endunless
            <a href="{{ route('trade.detail', $trade->RECORD_ID) }}" class="btn btn-primary">詳細を見る</a>
        </div>
    </div>
</div>
