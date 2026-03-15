{{--
============================================================
トップ画面 (home.blade.php)
============================================================

【対応画面定義】
  - index.csv（トップ画面 - index.php）

【このファイルの役割】
  - サイトのトップページを表示
  - 土地検索フォーム
  - 最近借りた土地の一覧（ログイン時のみ）

【受け取るデータ】
  - $recentRentals: ログインユーザーが最近借りた土地（5件まで）
    → HomeController@index から渡される
    → RentalRecordモデルのコレクション（landリレーション含む）

【画面構成】
  1. ヒーローセクション（検索フォーム）
     - フリーワード検索
     - あいまい検索（オン/オフ）
     - 利用日（カレンダー）
     - 都道府県（プルダウン）
     - 市区町村（テキスト入力）
     - 利用時間（開始・終了 15分刻み）
     - 料金上限
     - 料金単位（日/時間/15分あたり）
     - 面積
     - 地図から検索
     
  2. 最近借りた土地セクション
     - ログイン時: 土地カード5件表示
     - 未ログイン時: ログイン促進メッセージ

============================================================
--}}

{{-- layouts/app.blade.phpを継承（共通のHTML構造を使用） --}}
@extends('layouts.app')

{{-- ページタイトル（<title>タグの内容） --}}
@section('title', 'スキマパーク - あなたに合った土地を見つけよう')

{{-- メインコンテンツ開始 --}}
@section('content')
    {{-- 
    =====================================================
    ヒーローセクション
    =====================================================
    サイトのメインビジュアルエリア
    キャッチコピーと検索フォームを配置
    --}}
    <div class="hero">
        <div class="container">
            <h1>あなたに合った土地を見つけよう</h1>
            <p>使いたい人と貸したい人を繋ぐプラットフォーム</p>

            {{-- 
            検索フォーム
            action="/lands" へGETリクエストで検索パラメータを送信
            --}}
            <div class="search-box">
                <h2>検索する</h2>
                <form action="{{ url('/lands') }}" method="GET">
                    {{-- フリーワード検索 --}}
                    <div class="form-group">
                        <label class="form-label">フリーワード検索</label>
                        <input type="text" name="keyword" class="form-input" placeholder="例: 畑, 駐車場, イベントスペース">
                    </div>

                    {{-- あいまい検索 --}}
                    <div class="form-group">
                        <label class="form-label">あいまい検索</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="fuzzy" value="on" checked> オン
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="fuzzy" value="off"> オフ
                            </label>
                        </div>
                    </div>

                    {{-- 利用日 --}}
                    <div class="form-group">
                        <label class="form-label">利用日</label>
                        <input type="date" name="use_date" class="form-input">
                    </div>

                    {{-- 都道府県 --}}
                    <div class="form-group">
                        <label class="form-label">都道府県</label>
                        <select name="prefecture" class="form-select">
                            <option value="">すべての都道府県</option>
                            <option value="1">北海道</option>
                            <option value="2">青森県</option>
                            <option value="3">岩手県</option>
                            <option value="4">宮城県</option>
                            <option value="5">秋田県</option>
                            <option value="6">山形県</option>
                            <option value="7">福島県</option>
                            <option value="8">茨城県</option>
                            <option value="9">栃木県</option>
                            <option value="10">群馬県</option>
                            <option value="11">埼玉県</option>
                            <option value="12">千葉県</option>
                            <option value="13">東京都</option>
                            <option value="14">神奈川県</option>
                            <option value="15">新潟県</option>
                            <option value="16">富山県</option>
                            <option value="17">石川県</option>
                            <option value="18">福井県</option>
                            <option value="19">山梨県</option>
                            <option value="20">長野県</option>
                            <option value="21">岐阜県</option>
                            <option value="22">静岡県</option>
                            <option value="23">愛知県</option>
                            <option value="24">三重県</option>
                            <option value="25">滋賀県</option>
                            <option value="26">京都府</option>
                            <option value="27">大阪府</option>
                            <option value="28">兵庫県</option>
                            <option value="29">奈良県</option>
                            <option value="30">和歌山県</option>
                            <option value="31">鳥取県</option>
                            <option value="32">島根県</option>
                            <option value="33">岡山県</option>
                            <option value="34">広島県</option>
                            <option value="35">山口県</option>
                            <option value="36">徳島県</option>
                            <option value="37">香川県</option>
                            <option value="38">愛媛県</option>
                            <option value="39">高知県</option>
                            <option value="40">福岡県</option>
                            <option value="41">佐賀県</option>
                            <option value="42">長崎県</option>
                            <option value="43">熊本県</option>
                            <option value="44">大分県</option>
                            <option value="45">宮崎県</option>
                            <option value="46">鹿児島県</option>
                            <option value="47">沖縄県</option>
                        </select>
                    </div>

                    {{-- 市区町村 --}}
                    <div class="form-group">
                        <label class="form-label">市区町村</label>
                        <input type="text" name="city" class="form-input" placeholder="例: 渋谷区, 札幌市">
                    </div>

                    {{-- 利用時間 --}}
                    <div class="form-group">
                        <label class="form-label">利用時間</label>
                        <div class="time-range">
                            <div class="time-select">
                                <label class="sub-label">開始時刻</label>
                                <select name="start_time" class="form-select">
                                    <option value="">選択してください</option>
                                    @for($h = 0; $h < 24; $h++)
                                        @foreach(['00', '15', '30', '45'] as $m)
                                            <option value="{{ sprintf('%02d:%s', $h, $m) }}">{{ sprintf('%02d:%s', $h, $m) }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                            <span class="time-separator">〜</span>
                            <div class="time-select">
                                <label class="sub-label">終了時刻</label>
                                <select name="end_time" class="form-select">
                                    <option value="">選択してください</option>
                                    @for($h = 0; $h < 24; $h++)
                                        @foreach(['00', '15', '30', '45'] as $m)
                                            <option value="{{ sprintf('%02d:%s', $h, $m) }}">{{ sprintf('%02d:%s', $h, $m) }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 料金上限 --}}
                    <div class="form-group">
                        <label class="form-label">料金（上限）</label>
                        <div class="price-input-group">
                            <input type="number" name="price_max" class="form-input" placeholder="例: 50000" min="0" step="1000">
                            <span class="suffix">円まで</span>
                        </div>
                    </div>

                    {{-- 料金単位 --}}
                    <div class="form-group">
                        <label class="form-label">料金単位</label>
                        <select name="price_unit" class="form-select">
                            <option value="">選択してください</option>
                            <option value="day">日当たり</option>
                            <option value="hour">時間あたり</option>
                            <option value="15min">15分あたり</option>
                        </select>
                    </div>

                    {{-- 面積 --}}
                    <div class="form-group">
                        <label class="form-label">面積</label>
                        <div class="input-with-suffix">
                            <input type="number" name="area_min" class="form-input" placeholder="例: 100" min="0">
                            <span class="suffix">㎡以上</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">検索する</button>
                </form>

            </div>
        </div>
    </div>

    {{-- 
    =====================================================
    最近借りた土地セクション
    =====================================================
    
    【表示条件】
    - @auth: ログイン中のみ土地カードを表示
    - @else: 未ログイン時はログイン促進メッセージを表示
    
    【データソース】
    - $recentRentals: HomeControllerから渡されるRentalRecordコレクション
    - $rental->land: リレーションでLAND_TABLEの情報を取得
    
    【表示内容】
    - 土地画像（IMAGE）
    - 住所（CITY + STREET_ADDRESS）
    - レンタル期間（RENTAL_START_DATE 〜 RENTAL_END_DATE）
    - 料金（PRICE）
    --}}
    <div class="section section-light">
        <div class="container">
            <h2 class="section-title">最近借りた土地</h2>
            <div class="card-grid">
                {{-- ログイン中の場合: 最近借りた土地を表示 --}}
                @auth
                    {{-- $recentRentalsをループして土地カードを表示 --}}
                    {{-- @forelseは「データがある場合はループ、なければ@emptyブロック」という便利な構文 --}}
                    @forelse($recentRentals as $rental)
                        <div class="card">
                            {{-- 土地画像 --}}
                            <div class="card-image">
                                {{-- 画像がある場合は表示、なければプレースホルダー --}}
                                @if($rental->land->IMAGE ?? null)
                                    <img src="{{ asset('storage/' . $rental->land->IMAGE) }}" alt="{{ $rental->land->CITY ?? '土地' }}">
                                @else
                                    <div class="placeholder-image">土地の写真</div>
                                @endif
                            </div>
                            {{-- 土地情報 --}}
                            <div class="card-body">
                                {{-- 住所（市区町村 + 番地） --}}
                                <h3 class="card-title">{{ $rental->land->CITY ?? '' }}{{ $rental->land->STREET_ADDRESS ?? '' }}</h3>
                                {{-- レンタル期間（?-> はnullセーフ演算子: nullの場合はエラーにならない） --}}
                                <p class="card-text">
                                    {{ $rental->RENTAL_START_DATE?->format('Y/m/d') }} 〜 {{ $rental->RENTAL_END_DATE?->format('Y/m/d') }}
                                </p>
                                {{-- 料金（number_formatで3桁カンマ区切り） --}}
                                <div class="card-price">
                                    {{ number_format($rental->PRICE) }}円
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- 貸し出し記録がない場合 --}}
                        <p class="no-data">最近借りた土地はありません。</p>
                    @endforelse
                @else
                    {{-- 未ログインの場合: ログイン促進メッセージ --}}
                    <div class="login-prompt">
                        <p>ログインすると最近借りた土地が表示されます。</p>
                        {{-- route()ヘルパーでルート名からURLを生成 --}}
                        <a href="{{ route('login') }}" class="btn btn-primary">ログイン</a>
                        <a href="{{ route('register') }}" class="btn btn-outline">会員登録</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .hero {
        padding: 60px 0;
        text-align: center;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
    }
    .hero h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #222;
    }
    .hero p {
        font-size: 16px;
        color: #666;
        margin-bottom: 40px;
    }
    .search-box {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        max-width: 640px;
        margin: 0 auto;
        text-align: left;
    }
    .search-box h2 {
        font-size: 20px;
        margin-bottom: 24px;
        color: #333;
        text-align: center;
        font-weight: 600;
    }
    .btn-block {
        width: 100%;
        margin-top: 20px;
        padding: 14px;
        font-size: 16px;
    }
    .radio-group {
        display: flex;
        gap: 24px;
    }
    .radio-label {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        font-size: 14px;
    }
    .radio-label input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
    }
    .time-range {
        display: flex;
        align-items: flex-end;
        gap: 12px;
    }
    .time-select {
        flex: 1;
    }
    .time-separator {
        padding-bottom: 10px;
        color: #666;
    }
    .sub-label {
        display: block;
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }
    .price-input-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .price-input-group .form-input {
        flex: 1;
    }
    .input-with-suffix {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .input-with-suffix .form-input {
        flex: 1;
    }
    .suffix {
        white-space: nowrap;
        color: #666;
        font-size: 14px;
    }

    .section {
        padding: 60px 0;
    }
    .section-light {
        background: #fafafa;
    }
    .section-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 30px;
        color: #333;
    }
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }
    .card-scroll {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding-bottom: 16px;
    }
    .card-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .card-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .card-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .card-horizontal {
        min-width: 300px;
        flex-shrink: 0;
    }
    .card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }
    .card-image {
        height: 180px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
    }
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .placeholder-image {
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
        color: #333;
    }
    .card-text {
        font-size: 14px;
        color: #666;
        margin-bottom: 8px;
    }
    .card-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary);
    }
    .no-data {
        text-align: center;
        color: #666;
        grid-column: 1 / -1;
        padding: 40px;
    }
    .login-prompt {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        grid-column: 1 / -1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    .login-prompt p {
        font-size: 16px;
        color: #666;
        margin-bottom: 20px;
    }
    .login-prompt .btn {
        margin: 0 8px;
    }
    @media (max-width: 768px) {
        .hero h1 {
            font-size: 24px;
        }
        .search-box {
            padding: 24px 16px;
        }
        .time-range {
            flex-direction: column;
            gap: 8px;
        }
        .time-separator {
            display: none;
        }
    }
</style>
@endpush
