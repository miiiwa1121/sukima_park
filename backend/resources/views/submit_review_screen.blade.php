@extends('layouts.app')

@section('title', 'レビューを投稿する')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/submit_review.css') }}">
@endpush

@section('content')
    <div class="section">
      <a href="{{ route('trade.detail', $rental->RECORD_ID) }}" class="back-link">
        ← 詳細に戻る
      </a>

          <h1 class="page-title">レビューを投稿する</h1>

          <!-- レビュー対象のサマリー -->
          <div class="review-summary">
            <div class="summary-title">{{ $rental->land->CITY ?? '' }} {{ $rental->land->STREET_ADDRESS ?? '' }}</div>
            <div class="summary-period">
              利用期間: {{ $rental->RENTAL_START_DATE ? \Carbon\Carbon::parse($rental->RENTAL_START_DATE)->format('Y/m/d') : '-' }} 〜 {{ $rental->RENTAL_END_DATE ? \Carbon\Carbon::parse($rental->RENTAL_END_DATE)->format('Y/m/d') : '-' }}
            </div>
          </div>

          <!-- 注意事項 -->
          <div class="note-box">
            <div class="note-title">📝 レビュー投稿について</div>
            <div class="note-text">
              ・レビューは一度投稿すると編集できません。<br />
              ・他の利用者の参考になる、具体的で建設的なレビューをお願いします。<br />
              ・不適切な内容は運営により削除される場合があります。
            </div>
          </div>

          <!-- レビューフォーム -->
          <form action="{{ route('review.store', $rental->RECORD_ID) }}" method="POST" class="review-form-container">
            @csrf
            
            @if($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <!-- 土地の評価 -->
            <div class="form-section">
              <h2 class="section-heading">🏠 土地はいかがでしたか？</h2>

              <div class="form-group">
                <label class="form-label">
                  評価<span class="required">*必須</span>
                </label>
                <div class="star-rating" id="land-rating">
                  <span class="star" data-rating="1">★</span>
                  <span class="star" data-rating="2">★</span>
                  <span class="star" data-rating="3">★</span>
                  <span class="star" data-rating="4">★</span>
                  <span class="star" data-rating="5">★</span>
                </div>
                <input
                  type="hidden"
                  name="land_rating"
                  id="land-rating-value"
                  value="{{ old('land_rating') }}"
                  required
                />
                <div class="rating-label" id="land-rating-label">
                  星をクリックして評価してください
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="land-comment">
                  コメント（任意）
                </label>
                <textarea
                  id="land-comment"
                  name="land_comment"
                  class="form-control"
                  placeholder="例：駅から近く、屋根付きで雨の日も安心でした。清潔に管理されていて快適に利用できました。"
                  maxlength="500"
                >{{ old('land_comment') }}</textarea>
                <div class="char-count" id="land-char-count">0 / 500</div>
                <div class="form-hint">
                  土地の立地、設備、清潔さ、使いやすさなどについてお書きください
                </div>
              </div>
            </div>

            <!-- 貸し手の評価 -->
            <div class="form-section">
              <h2 class="section-heading">👤 貸し手（{{ $rental->land->owner->USERNAME ?? '貸し手' }}さん）の対応はいかがでしたか？</h2>

              <div class="form-group">
                <label class="form-label">
                  評価<span class="required">*必須</span>
                </label>
                <div class="star-rating" id="owner-rating">
                  <span class="star" data-rating="1">★</span>
                  <span class="star" data-rating="2">★</span>
                  <span class="star" data-rating="3">★</span>
                  <span class="star" data-rating="4">★</span>
                  <span class="star" data-rating="5">★</span>
                </div>
                <input
                  type="hidden"
                  name="owner_rating"
                  id="owner-rating-value"
                  value="{{ old('owner_rating') }}"
                  required
                />
                <div class="rating-label" id="owner-rating-label">
                  星をクリックして評価してください
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="owner-comment">
                  コメント（任意）
                </label>
                <textarea
                  id="owner-comment"
                  name="owner_comment"
                  class="form-control"
                  placeholder="例：丁寧に対応していただきました。メッセージの返信も早く、スムーズに取引できました。"
                  maxlength="500"
                >{{ old('owner_comment') }}</textarea>
                <div class="char-count" id="owner-char-count">0 / 500</div>
                <div class="form-hint">
                  連絡のやり取り、対応の丁寧さ、信頼性などについてお書きください
                </div>
              </div>
            </div>

            <!-- フォームアクション -->
            <div class="form-actions">
              <button type="submit" class="submit-btn">
                レビューを送信する
              </button>
              <a href="{{ route('trade.detail', $rental->RECORD_ID) }}" class="cancel-btn">
                キャンセル
              </a>
            </div>
          </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/submit_review.js') }}"></script>
@endpush
