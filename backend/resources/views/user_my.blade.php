{{--
============================================================
マイページ画面 (user_my.blade.php)
============================================================

【対応画面定義】
  - user_my.csv
  - my_profile_screen.html（HTMLテンプレート）

【このファイルの役割】
  - ログインユーザーのプロフィール表示
  - 各種メニューへのナビゲーション
  - 公開中の土地一覧

【受け取るデータ】
  - $user: ログインユーザー（Memberモデル）
  - $publicLands: ログインユーザーの公開土地（Landモデルのコレクション）

============================================================
--}}

{{-- 
  @extends: レイアウトファイルを継承
  layouts/app.blade.php の中身をベースにして、@section部分を差し替える
--}}
@extends('layouts.app')

{{-- 
  @section('title', ...): ページタイトルを設定
  layouts/app.blade.php の <title>@yield('title')</title> に埋め込まれる
--}}
@section('title', 'マイページ - スキマパーク')

{{-- 
  @section('content'): メインコンテンツ開始
  layouts/app.blade.php の @yield('content') に埋め込まれる
--}}
@section('content')
    <main>
      <section class="container profile-wrapper" style="border-top: 1px solid #e0e0e0; margin-top: 20px;">
        <div class="profile-inner">
          {{-- 1. アイコン画像 --}}
          <div class="avatar">
            {{-- 
              @if(条件): 条件分岐
              Auth::user() でログインユーザーのモデルを取得
              Auth::user()->ICON_IMAGE でICON_IMAGEカラムの値を取得
              値があれば画像を表示、なければユーザー名の頭文字を表示
            --}}
            @if(Auth::user()->ICON_IMAGE)
              {{-- 
                {{ asset('storage/' . ...) }}: publicディレクトリへのパスを生成
                例: /storage/icons/user1.jpg のようなURLになる
              --}}
              <img src="{{ asset('storage/' . Auth::user()->ICON_IMAGE) }}" alt="アイコン" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            @else
              {{-- 
                mb_substr(): マルチバイト対応の文字列切り出し関数
                Auth::user()->USERNAME の最初の1文字を取得
                ?? 'U': USERNAMEがnullの場合は'U'を表示（null合体演算子）
              --}}
              {{ mb_substr(Auth::user()->USERNAME ?? 'U', 0, 1) }}
            @endif
          </div>

          <div class="profile-info">
            {{-- 2. ユーザー名 --}}
            <h1 style="font-size: 24px; font-weight: 700; color: #222">
              {{-- 
                {{ ... }}: 変数を出力（自動的にHTMLエスケープされる）
                Auth::user()->USERNAME: ログインユーザーのUSERNAMEカラムを表示
              --}}
              {{ Auth::user()->USERNAME }}
            </h1>

            {{-- 3. 自己紹介 --}}
            <p style="margin-top: 12px; color: #555">
              {{-- 
                ?? (null合体演算子): 左辺がnullの場合、右辺を使用
                SELF_INTRODUCTIONがnullなら「自己紹介が設定されていません...」を表示
              --}}
              {{ Auth::user()->SELF_INTRODUCTION ?? '自己紹介が設定されていません。プロフィール編集から設定できます。' }}
            </p>

            {{-- 4. 公開土地数 --}}
            <div class="profile-stats">
              <div>公開土地 <span>
                {{-- 
                  $publicLands->count(): コレクションの件数を取得
                  $publicLandsはUserControllerから渡されたLandモデルのコレクション
                --}}
                {{ $publicLands->count() }}件
              </span></div>
            </div>

            {{-- 5. プロフィール編集 / 6. 自己保持土地一覧 --}}
            <div class="profile-actions">
              <a href="{{ route('prof_custom') }}" class="btn btn-secondary">プロフィール編集</a>
              <a href="{{ route('my_land_list') }}" class="btn btn-secondary">自己保持土地一覧</a>
            </div>
          </div>
        </div>

        {{-- 7. レンタル中一覧 / 8. 取引完了一覧 --}}
        <div class="container" style="padding-bottom: 24px">
          <div class="nav-quick">
            <a href="{{ route('rental_list') }}" class="nav-card">レンタル中一覧</a>
            <a href="{{ route('trade_fin_list') }}" class="nav-card">取引完了一覧</a>
          </div>
        </div>
      </section>

      {{-- 9. 公開中の土地 --}}
      <section class="section">
        <div class="container">
          <div class="section-header">
            <h2 class="section-title">公開中の土地</h2>
            <a href="{{ route('my_land_list') }}" class="btn btn-secondary" style="padding: 6px 14px">一覧を見る</a>
          </div>

          {{-- 10-14. 公開中土地カード --}}
          <div class="card-grid">
            {{-- 
              @php ... @endphp: Bladeテンプレート内でPHPコードを実行
              都道府県コード(1-47)を都道府県名に変換するための配列を定義
              LAND_TABLEのPEREFECTURESカラムには数値(1-47)が格納されている
            --}}
            @php
              // 都道府県コードと名前の対応配列
              // 例: 1 => '北海道', 13 => '東京都' など
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

            {{-- 
              @forelse($publicLands as $land): ループ処理
              $publicLands（土地のコレクション）を1件ずつ$landに代入してループ
              
              @forelseの特徴:
              - データがある場合: ループを実行
              - データが0件の場合: @emptyブロックを実行
              
              ループ回数 = $publicLandsの件数
              例: 3件の土地があれば、土地カードが3枚表示される
            --}}
            @forelse($publicLands as $land)
              {{-- 
                公開中土地カード（画面項目No.10）
                クリックで土地詳細画面へ遷移（loan_detail）
              --}}
              <a href="{{ route('loan_detail', $land->LAND_ID) }}" class="card" style="text-decoration: none">
                {{-- 画面項目No.11: 土地写真 --}}
                <div class="card-image">
                  @if($land->IMAGE)
                    <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="{{ $land->CITY }}" style="width:100%;height:100%;object-fit:cover;">
                  @else
                    土地写真
                  @endif
                </div>
                <div class="card-body">
                  {{-- 画面項目No.13: 土地住所 --}}
                  <div class="tag">
                    {{ $prefectures[$land->PEREFECTURES] ?? '' }}・{{ $land->CITY }}
                  </div>
                  {{-- 画面項目No.12: 土地タイトル --}}
                  <h3 class="card-title">
                    {{ $land->CITY }}{{ $land->STREET_ADDRESS }}
                  </h3>
                  {{-- 画面項目No.14: 土地料金 --}}
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
            {{-- 
              @empty: $publicLandsが空（0件）の場合に表示される
              @forelseの一部で、データがない時の代替表示を定義
            --}}
            @empty
              <p style="text-align: center; color: #666; grid-column: 1 / -1; padding: 40px;">
                公開中の土地はありません。土地を登録してみましょう！
              </p>
            {{-- @endforelse: ループの終了 --}}
            @endforelse
          </div>
        </div>
      </section>
    </main>
{{-- @endsection: @section('content')の終了 --}}
@endsection

{{-- 
  @push('styles'): スタックにCSSを追加
  layouts/app.blade.phpの @stack('styles') の位置に出力される
  ページ固有のCSSを追加するための仕組み
--}}
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
  .nav-quick {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
  }
  .nav-card {
    background: #f1f8e9;
    border: 1px solid #c5e1a5;
    border-radius: 8px;
    padding: 16px;
    text-decoration: none;
    color: #2e7d32;
    font-weight: 500;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .nav-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
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
  .like-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 10;
  }
  .like-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }
  .like-btn.liked {
    color: #e91e63;
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
  .card-text {
    font-size: 14px;
    color: #666;
    margin-bottom: 12px;
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
    .nav-quick {
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }
  }
</style>
{{-- @endpush: @push('styles')の終了 --}}
@endpush
