{{--
============================================================
レンタルカード コンポーネント (rental-card.blade.php)
============================================================

【このコンポーネントの役割】
  - 1つのレンタル記録を表示するカード

【受け取るプロパティ】
  - $rental: RentalRecord モデルのインスタンス

============================================================
--}}

<div class="rental-card">
    <div class="rental-card-image">
        @if($rental->land && $rental->land->IMAGE)
            <img src="{{ asset('storage/' . $rental->land->IMAGE) }}" alt="{{ $rental->land->CITY }} {{ $rental->land->STREET_ADDRESS }}">
        @else
            <div style="background: #f0f0f0; height: 200px; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                📍
            </div>
        @endif
    </div>
    <div class="rental-card-body">
        <h3 class="rental-card-title">
            {{ $rental->land->CITY ?? '不明' }} {{ $rental->land->STREET_ADDRESS ?? '' }}
        </h3>
        <div class="rental-card-info">
            <div class="info-row">
                <span class="info-label">レンタル期間:</span>
                <span class="info-value rental-period">
                    {{ $rental->RENTAL_START_DATE->format('Y年m月d日') }}
                    ～
                    {{ $rental->RENTAL_END_DATE->format('Y年m月d日') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">面積:</span>
                <span class="info-value">{{ $rental->land->AREA ?? '不明' }}m²</span>
            </div>
        </div>
        <div class="rental-price">
            ¥{{ number_format($rental->PRICE) }}/{{ ['日', '時間', '15分'][$rental->PRICE_UNIT] ?? '日' }}
        </div>
        <div class="rental-card-footer">
            <a href="{{ route($detailRoute ?? 'rental_list.show', $rental->RECORD_ID) }}" class="btn btn-primary">詳細を見る</a>
        </div>
    </div>
</div>
