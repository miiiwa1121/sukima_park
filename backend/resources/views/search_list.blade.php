{{--
============================================================
検索結果表示画面 (search_list.blade.php)
============================================================

【対応画面定義】
- search_list_items.json
- 検索結果表示画面.html

【このファイルの役割】
- 土地検索結果の一覧表示
- 絞り込み条件によるフィルタリング
- 並び替え機能
- ページネーション（20件/ページ）

【受け取るデータ】
- $lands: 検索結果の土地データ（ページネーション済み）
- $filters: 現在の検索条件

============================================================
--}}

@extends('layouts.app')

@section('title', '検索結果')

@section('content')
    <div class="results-wrapper">
        {{-- サイドバー（絞り込み条件） --}}
        <aside class="sidebar">
            <div class="sidebar-box">
                <h2 class="sidebar-title">条件で絞り込む</h2>
                <form action="{{ route('lands.index') }}" method="GET" id="search-form">
                    {{-- フリーワード検索 --}}
                    <div class="form-group">
                        <label for="keyword">フリーワード検索</label>
                        <input type="text" id="keyword" name="keyword" class="form-control" placeholder="例: 畑, 駐車場"
                            value="{{ request('keyword', '') }}">
                    </div>

                    {{-- あいまい検索（チェックボックス） --}}
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="fuzzy" value="1" {{ request('fuzzy') ? 'checked' : '' }}>
                            <span>あいまい検索</span>
                        </label>
                    </div>

                    {{-- 都道府県 --}}
                    <div class="form-group">
                        <label for="prefecture">都道府県</label>
                        <select id="prefecture" name="prefecture" class="form-control">
                            <option value="">すべての都道府県</option>
                            @php
                                $prefectures = [
                                    1 => '北海道',
                                    2 => '青森県',
                                    3 => '岩手県',
                                    4 => '宮城県',
                                    5 => '秋田県',
                                    6 => '山形県',
                                    7 => '福島県',
                                    8 => '茨城県',
                                    9 => '栃木県',
                                    10 => '群馬県',
                                    11 => '埼玉県',
                                    12 => '千葉県',
                                    13 => '東京都',
                                    14 => '神奈川県',
                                    15 => '新潟県',
                                    16 => '富山県',
                                    17 => '石川県',
                                    18 => '福井県',
                                    19 => '山梨県',
                                    20 => '長野県',
                                    21 => '岐阜県',
                                    22 => '静岡県',
                                    23 => '愛知県',
                                    24 => '三重県',
                                    25 => '滋賀県',
                                    26 => '京都府',
                                    27 => '大阪府',
                                    28 => '兵庫県',
                                    29 => '奈良県',
                                    30 => '和歌山県',
                                    31 => '鳥取県',
                                    32 => '島根県',
                                    33 => '岡山県',
                                    34 => '広島県',
                                    35 => '山口県',
                                    36 => '徳島県',
                                    37 => '香川県',
                                    38 => '愛媛県',
                                    39 => '高知県',
                                    40 => '福岡県',
                                    41 => '佐賀県',
                                    42 => '長崎県',
                                    43 => '熊本県',
                                    44 => '大分県',
                                    45 => '宮崎県',
                                    46 => '鹿児島県',
                                    47 => '沖縄県',
                                ];
                            @endphp
                            @foreach($prefectures as $id => $name)
                                <option value="{{ $id }}" {{ request('prefecture') == $id ? 'selected' : '' }}>{{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 市区町村 --}}
                    <div class="form-group">
                        <label for="city">市区町村</label>
                        <input type="text" id="city" name="city" class="form-control" placeholder="例: 渋谷区"
                            value="{{ request('city', '') }}">
                    </div>

                    {{-- 利用時間帯 --}}
                    <div class="form-group">
                        <label>利用時間帯</label>
                        <div class="time-range-group">
                            <select id="time_start" name="time_start" class="form-control">
                                <option value="">開始時刻</option>
                                @for($h = 0; $h < 24; $h++)
                                    @foreach(['00', '15', '30', '45'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" {{ request('time_start') === $time ? 'selected' : '' }}>
                                            {{ $time }}</option>
                                    @endforeach
                                @endfor
                            </select>
                            <span class="time-separator">〜</span>
                            <select id="time_end" name="time_end" class="form-control">
                                <option value="">終了時刻</option>
                                @for($h = 0; $h < 24; $h++)
                                    @foreach(['00', '15', '30', '45'] as $m)
                                        @php $time = sprintf('%02d:%s', $h, $m); @endphp
                                        <option value="{{ $time }}" {{ request('time_end') === $time ? 'selected' : '' }}>{{ $time }}
                                        </option>
                                    @endforeach
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- 料金（上限） --}}
                    <div class="form-group">
                        <label for="price_max">料金（上限）</label>
                        <div class="input-with-suffix">
                            <input type="number" id="price_max" name="price_max" class="form-control" placeholder="例: 50000"
                                min="0" step="1000" value="{{ request('price_max', '') }}">
                            <span class="suffix">円まで</span>
                        </div>
                    </div>

                    {{-- 面積 --}}
                    <div class="form-group">
                        <label for="area_min">面積</label>
                        <div class="input-with-suffix">
                            <input type="number" id="area_min" name="area_min" class="form-control" placeholder="例: 100"
                                min="0" step="1" value="{{ request('area_min', '') }}">
                            <span class="suffix">㎡以上</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary search-btn">再検索する</button>
                </form>
            </div>
        </aside>

        {{-- メインコンテンツ（検索結果） --}}
        <section class="results-main">
            {{-- 検索結果ヘッダー --}}
            <div class="results-header">
                <div class="results-count">
                    @if(request('keyword') || request('city'))
                        「{{ request('keyword') ?: request('city') }}」の検索結果:
                    @else
                        検索結果:
                    @endif
                    <span>{{ $lands->total() }}</span>件
                </div>

                <div class="sort-group">
                    <label for="sort_order">並び替え:</label>
                    <select id="sort_order" name="sort" class="form-control" onchange="updateSort(this.value)">
                        <option value="recommend" {{ request('sort') === 'recommend' ? 'selected' : '' }}>おすすめ順</option>
                        <option value="rating_desc" {{ request('sort') === 'rating_desc' ? 'selected' : '' }}>評価（高い順）</option>
                        <option value="rating_asc" {{ request('sort') === 'rating_asc' ? 'selected' : '' }}>評価（低い順）</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>料金（安い順）</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>料金（高い順）</option>
                        <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>面積（広い順）</option>
                        <option value="area_asc" {{ request('sort') === 'area_asc' ? 'selected' : '' }}>面積（狭い順）</option>
                    </select>
                </div>
            </div>

            {{-- 検索結果一覧 --}}
            @if($lands->count() > 0)
                <div class="card-grid">
                    @foreach($lands as $land)
                        <a href="{{ route('lands.show', $land->LAND_ID) }}" class="card">
                            <div class="card-image">
                                @if($land->IMAGE)
                                    <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="{{ $land->NAME ?? '土地' }}">
                                @else
                                    <span>土地の写真</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h3 class="card-title">{{ $land->NAME ?? $land->CITY . $land->STREET_ADDRESS }}</h3>
                                <p class="card-text">
                                    @php
                                        $prefName = $prefectures[$land->PEREFECTURES] ?? '';
                                    @endphp
                                    {{ $prefName }}{{ $land->CITY }}
                                </p>
                                <div class="card-price">
                                    @php
                                        $priceUnit = match ($land->PRICE_UNIT) {
                                            0 => '日額',
                                            1 => '時間',
                                            2 => '15分',
                                            default => ''
                                        };
                                    @endphp
                                    {{ $priceUnit }} {{ number_format($land->PRICE) }}円
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- ページネーション --}}
                <div class="pagination-wrapper">
                    {{ $lands->appends(request()->query())->links('components.custom-pagination') }}
                </div>
            @else
                {{-- 検索結果0件 --}}
                <div class="no-results">
                    <h3>検索結果がありません</h3>
                    <p>条件を変更して再度お試しください。</p>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('styles')
    <style>
        /* 検索結果ページ専用スタイル */
        .results-wrapper {
            display: flex;
            gap: 24px;
            padding-top: 32px;
            padding-bottom: 40px;
            align-items: flex-start;
        }

        /* サイドバー */
        .sidebar {
            width: 300px;
            flex-shrink: 0;
            position: sticky;
            top: 84px;
        }

        .sidebar-box {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            padding: 24px;
            border: 1px solid var(--border);
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        /* フォーム */
        .form-group {
            margin-bottom: 16px;
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
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 16px;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            padding: 8px 12px;
            background: var(--bg-light);
            border-radius: 6px;
            transition: background 0.2s;
        }

        .checkbox-label:hover {
            background: var(--bg-gray);
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .time-range-group {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 8px;
            align-items: center;
        }

        .time-separator {
            color: var(--text-gray);
        }

        .input-with-suffix {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-with-suffix .form-control {
            flex: 1;
        }

        .suffix {
            white-space: nowrap;
            color: var(--text-gray);
            font-size: 14px;
        }

        .search-btn {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            font-size: 16px;
            font-weight: 600;
        }

        /* メインコンテンツ */
        .results-main {
            flex-grow: 1;
            min-width: 0;
        }

        /* 検索結果ヘッダー */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 12px 16px;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
        }

        .results-count {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .results-count span {
            color: var(--primary);
            font-size: 20px;
        }

        .sort-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sort-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-gray);
        }

        .sort-group .form-control {
            width: auto;
        }

        /* カードグリッド */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid var(--border);
            transition: box-shadow 0.2s, transform 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }

        .card-image {
            width: 100%;
            height: 180px;
            background: var(--bg-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 14px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            padding: 16px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .card-text {
            font-size: 14px;
            color: var(--text-gray);
            margin-bottom: 12px;
        }

        .card-price {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
        }

        /* ページネーション */
        .pagination-wrapper {
            margin-top: 32px;
            display: flex;
            justify-content: center;
        }

        .custom-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        /* 結果表示テキスト: ( 21 ~ 40 of 100 ) */
        .pagination-info {
            font-size: 14px;
            color: var(--text-gray);
        }

        /* ページネーションリンクのコンテナ */
        .pagination-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ページ番号共通スタイル */
        .pagination-number {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            color: var(--text-dark);
            border: none;
            background: transparent;
        }

        .pagination-number:hover {
            color: var(--primary);
        }

        /* 現在のページ */
        .pagination-number.active {
            color: var(--primary);
            font-weight: bold;
            background: transparent;
            border: none;
        }

        /* 矢印 (< >) - 数字の1.5倍の大きさ */
        .pagination-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 8px;
            font-size: 21px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.2s;
            color: var(--text-dark);
            border: none;
            background: transparent;
        }

        .pagination-arrow:hover {
            color: var(--primary);
        }

        /* ... のスタイル */
        .pagination-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            font-size: 14px;
            color: var(--text-gray);
        }

        /* 検索結果0件 */
        .no-results {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 60px 40px;
            text-align: center;
            color: var(--text-gray);
        }

        .no-results h3 {
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        /* レスポンシブ */
        @media (max-width: 900px) {
            .results-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
            }

            .results-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // 並び替え変更時の処理
        function updateSort(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }
    </script>
@endpush