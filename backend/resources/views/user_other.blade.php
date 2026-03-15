{{--
============================================================
他ユーザープロフィール画面 (user_other.blade.php)
============================================================

【対応画面定義】
  - user_other.csv
  - user_my.blade.php（ベースとなるマイページ画面）

【このファイルの役割】
  - 他ユーザーのプロフィール表示
  - 公開中の土地一覧

【受け取るデータ】
  - $user: 表示対象ユーザー（Memberモデル）
  - $publicLands: 対象ユーザーの公開土地（Landモデルのコレクション）

============================================================
--}}

{{-- 
  @extends: レイアウトファイルを継承
  layouts/app.blade.php の中身をベースにして、@section部分を差し替える
--}}
@extends('layouts.app')

{{-- 
  @section('title', ...): ページタイトルを設定
--}}
@section('title', ($user->USERNAME ?? 'ユーザー') . 'さんのプロフィール - スキマパーク')

{{-- 
  @section('content'): メインコンテンツ開始
--}}
@section('content')
    <main>
      <section class="container profile-wrapper" style="border-top: 1px solid #e0e0e0; margin-top: 20px;">
        <div class="profile-inner">
          {{-- 1. アイコン画像 --}}
          <div class="avatar">
            @if($user->ICON_IMAGE)
              <img src="{{ asset('storage/' . $user->ICON_IMAGE) }}" alt="アイコン" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @else
              {{ mb_substr($user->USERNAME ?? 'U', 0, 1) }}
            @endif
          </div>

          <div class="profile-info">
            {{-- 2. ユーザー名 --}}
            <h1 style="font-size: 24px; font-weight: 700; color: #222">
              {{ $user->USERNAME }}
            </h1>

            {{-- 3. 自己紹介 --}}
            <p style="margin-top: 12px; color: #555">
              {{ $user->SELF_INTRODUCTION ?? '自己紹介が設定されていません。' }}
            </p>

            {{-- 4. 公開土地数 --}}
            <div class="profile-stats">
              <div>公開土地 <span>{{ $publicLands->count() }}件</span></div>
            </div>

            {{-- 5. アクションボタン（メッセージを送る） --}}
            <div class="profile-actions">
              <a href="{{ route('messages.show', $user->USER_ID) }}" class="btn btn-primary">💬 メッセージを送る</a>
            </div>
          </div>
        </div>
      </section>

      {{-- 公開中の土地 --}}
      <section class="section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">公開中の土地</h2>
          </div>

          <div class="card-grid">
            @php
              $prefectures = [
                1 => '北海道', 2 => '青森県', 3 => '岩手県', 4 => '宮城県',
                5 => '秋田県', 6 => '山形県', 7 => '福島県', 8 => '茨城県',
                9 => '栃木県', 10 => '群馬県', 11 => '埼玉県', 12 => '千葉県',
                13 => '東京都', 14 => '神奈川県', 15 => '新潟県', 16 => '富山県',
                17 => '石川県', 18 => '福井県', 19 => '山梨県', 20 => '長野県',
                21 => '岐阜県', 22 => '静岡県', 23 => '愛知県', 24 => '三重県',
                25 => '滋賀県', 26 => '京都府', 27 => '大阪府', 28 => '兵庫県',
                29 => '奈良県', 30 => '和歌山県', 31 => '鳥取県', 32 => '島根県',
                33 => '岡山県', 34 => '広島県', 35 => '山口県', 36 => '徳島県',
                37 => '香川県', 38 => '愛媛県', 39 => '高知県', 40 => '福岡県',
                41 => '佐賀県', 42 => '長崎県', 43 => '熊本県', 44 => '大分県',
                45 => '宮崎県', 46 => '鹿児島県', 47 => '沖縄県',
              ];
            @endphp

            @forelse($publicLands as $land)
              <a href="{{ route('loan_detail', $land->LAND_ID) }}" class="card" style="text-decoration: none">
                <div class="card-image">
                  @if($land->IMAGE)
                    <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="{{ $land->CITY }}" style="width:100%;height:100%;object-fit:cover;">
                  @else
                    土地写真
                  @endif
                </div>
                <div class="card-body">
                  <div class="tag">
                    {{ $prefectures[$land->PEREFECTURES] ?? '' }}・{{ $land->CITY }}
                  </div>
                  <h3 class="card-title">
                    {{ $land->CITY }}{{ $land->STREET_ADDRESS }}
                  </h3>
                  <div class="card-footer">
                    <span>
                      @if($land->PRICE)
                        ¥{{ number_format($land->PRICE) }}
                        @if($land->PRICE_UNIT == 0) / 日
                        @elseif($land->PRICE_UNIT == 1) / 時間
                        @elseif($land->PRICE_UNIT == 2) / 15分
                        @endif
                      @else
                        要相談
                      @endif
                    </span>
                  </div>
                </div>
              </a>
            @empty
              <p style="text-align: center; color: #666; grid-column: 1 / -1; padding: 40px;">
                公開中の土地はありません。
              </p>
            @endforelse
          </div>
        </div>
      </section>
    </main>
@endsection

@push('styles')
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  main {
    padding-bottom: 60px;
  }
  .profile-cover {
    background: linear-gradient(120deg, #a5d6a7, #81c784);
    height: 180px;
  }
  .profile-wrapper {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-top: none;
  }
  .profile-inner {
    display: flex;
    gap: 20px;
    padding: 24px 20px;
    position: relative;
    margin-top: 0;
  }
  .avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid #fff;
    background: #c8e6c9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #2e7d32;
    flex-shrink: 0;
    overflow: hidden;
  }
  .profile-info {
    flex: 1;
  }
  .profile-meta {
    display: flex;
    gap: 16px;
    margin-top: 8px;
    color: #666;
    font-size: 14px;
  }
  .profile-stats {
    display: flex;
    gap: 24px;
    margin-top: 16px;
  }
  .profile-stats span {
    font-weight: 600;
    color: #2e7d32;
  }
  .profile-actions {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }
  .section {
    padding: 40px 0;
  }
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  .section-title {
    font-size: 20px;
    font-weight: 600;
    color: #222;
  }
  .card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }
  .card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    transition: box-shadow 0.2s;
    position: relative;
  }
  .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  .card-image {
    width: 100%;
    height: 160px;
    background: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 14px;
  }
  .card-body {
    padding: 16px;
  }
  .card-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #222;
  }
  .card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: #2e7d32;
  }
  .tag {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 8px;
  }
  .btn {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }
  .btn-primary {
    background: #2e7d32;
    color: #fff;
  }
  .btn-primary:hover {
    background: #1b5e20;
  }
  .btn-secondary {
    background: #f5f5f5;
    color: #333;
  }
  .btn-secondary:hover {
    background: #e0e0e0;
  }
  @media (max-width: 768px) {
    .profile-inner {
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .profile-actions {
      justify-content: center;
    }
    .profile-meta,
    .profile-stats {
      justify-content: center;
    }
  }
</style>
@endpush
