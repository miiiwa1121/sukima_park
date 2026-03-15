{{--
============================================================
貸出中詳細画面 (loan_detail.blade.php)
============================================================

【画面の概要】
  公開中の土地に対する予約状況や利用者情報を確認する画面です。
  土地オーナーのみがアクセスできます。

【対応画面定義】
  - context/画面レイアウト/my_land_detail_screen.html（HTMLテンプレート）

【このファイルの役割】
  1. 土地の募集概要を表示（土地名、所在地、面積、賃料など）
  2. 直近の貸出スケジュールを表示（将来実装予定）
  3. アクティビティログを表示（将来実装予定）
  4. ダッシュボード（閲覧数、問い合わせ数など）を表示

【LandControllerから受け取るデータ】
  - $land: 表示対象の土地（Landモデル）
    ※ LandController@show で LAND_ID をもとに取得
    ※ 所有者チェック済み（自分の土地のみアクセス可能）
  - $prefectures: 都道府県コードと名前の対応配列

【Landモデルの主要プロパティ】
  - LAND_ID: 土地ID（主キー）
  - NAME: 土地名（NULL可）
  - PEREFECTURES: 都道府県コード（1-47）
  - CITY: 市区町村
  - STREET_ADDRESS: 番地
  - AREA: 面積（㎡）
  - DESCRIPTION: 説明文
  - STATUS: 公開状態（0=非公開, 1=公開中）
  - PRICE: 賃料
  - PRICE_UNIT: 料金単位（0=日, 1=時間, 2=15分）

【使用しているBlade機能】
  - @extends: layouts.appを継承してヘッダー・フッターを自動表示
  - @section: ページタイトルとメインコンテンツを定義
  - {{ }}: 変数の表示（XSSエスケープ済み）
  - route(): ルート名からURLを生成
  - @push('styles'): このページ専用のCSSを追加

【将来の実装予定】
  - RENTAL_RECORD_TABLE からの貸出スケジュール取得
  - アクティビティログの記録と表示
  - 閲覧数などの統計データ

============================================================
--}}

{{-- ============================================================
    1. レイアウトの継承
    layouts/app.blade.php を継承し、共通のヘッダー・フッターを表示
============================================================ --}}
@extends('layouts.app')

{{-- ============================================================
    2. ページタイトルの設定
============================================================ --}}
@section('title', '貸出中詳細 - スキマパーク')

{{-- ============================================================
    3. メインコンテンツ
============================================================ --}}
@section('content')
    <main>
      <section class="container">
        
        {{-- ============================================================
            ページヘッダー
            画面タイトル、説明文、公開ステータスバッジを表示
        ============================================================ --}}
        <div class="page-header">
          <div>
            <h1>貸出中詳細</h1>
            <p style="color: #555; margin-top: 6px">
              公開中の土地に対する予約状況や利用者情報を確認できます。掲載を停止
              する場合は公開ステータスを変更してください。
            </p>
          </div>
          
          {{-- 
            公開ステータスバッジ
            $land->STATUS で現在の状態を表示
          --}}
          <div class="badge">
            @if($land->STATUS == 1)
              公開中 / 募集中
            @else
              非公開
            @endif
          </div>
        </div>

        {{-- ============================================================
            2カラムレイアウト
            左: メインパネル（募集概要、スケジュール、ログ）
            右: サイドバー（ダッシュボード、アクションボタン）
        ============================================================ --}}
        <div class="layout">
          
          {{-- ============================================================
              メインパネル（左側）
          ============================================================ --}}
          <div class="panel" style="display: grid; gap: 24px">
            
            {{-- ============================================================
                募集概要セクション
                土地の基本情報をテーブル形式で表示
            ============================================================ --}}
            <section>
              <h2>募集概要</h2>
              <table class="info-table">
                <tr>
                  <th>土地名</th>
                  {{-- 
                    土地名がNULLの場合は「市区町村 + 番地」を代わりに表示
                    ?? は Null合体演算子
                  --}}
                  <td>{{ $land->NAME ?? ($land->CITY . $land->STREET_ADDRESS) }}</td>
                </tr>
                <tr>
                  <th>所在地</th>
                  {{-- 
                    都道府県コードを名前に変換して表示
                    $prefectures[13] → '東京都'
                  --}}
                  <td>{{ $prefectures[$land->PEREFECTURES] ?? '' }}{{ $land->CITY }}{{ $land->STREET_ADDRESS }}</td>
                </tr>
                <tr>
                  <th>面積</th>
                  <td>{{ $land->AREA ?? '-' }}㎡</td>
                </tr>
                <tr>
                  <th>賃料</th>
                  <td>
                    {{-- 
                      価格表示ロジック:
                      1. PRICEがある場合: 金額 + 単位（/日, /時間, /15分）
                      2. PRICEがない場合:「要相談」と表示
                      
                      number_format() で桁区切りを追加
                      例: 20000 → "20,000"
                    --}}
                    @if($land->PRICE)
                      ¥{{ number_format($land->PRICE) }}
                      @if($land->PRICE_UNIT == 0) / 日
                      @elseif($land->PRICE_UNIT == 1) / 時間
                      @elseif($land->PRICE_UNIT == 2) / 15分
                      @endif
                    @else
                      要相談
                    @endif
                  </td>
                </tr>
              </table>
            </section>

            {{-- ============================================================
                直近の貸出スケジュールセクション
                
                【将来の実装予定】
                RENTAL_RECORD_TABLE から該当土地の予約情報を取得して表示
                - 利用期間
                - 利用者情報
                - 対応状況（入金待ち、利用中、完了など）
            ============================================================ --}}
            <section>
              <h2>直近の貸出スケジュール</h2>
              <div class="reservation-list">
                <p style="color: #666; font-size: 14px;">
                  現在、貸出予約はありません。
                </p>
                {{-- 
                  TODO: 将来的には以下のようなデータを表示
                  @foreach($rentalRecords as $record)
                    <div class="reservation-card">
                      <div><strong>{{ $record->START_DATE }} - {{ $record->END_DATE }}</strong></div>
                      <div>利用者: {{ $record->user->USERNAME }}</div>
                    </div>
                  @endforeach
                --}}
              </div>
            </section>

            {{-- ============================================================
                アクティビティログセクション
                
                【将来の実装予定】
                土地に関する活動履歴を表示
                - 問い合わせ受信
                - 入金確認
                - 利用開始/終了
                - レビュー投稿
            ============================================================ --}}
            <section>
              <h2>アクティビティログ</h2>
              <div class="timeline">
                <p style="color: #666; font-size: 14px;">
                  アクティビティはまだありません。
                </p>
                {{-- 
                  TODO: 将来的には以下のようなデータを表示
                  @foreach($activities as $activity)
                    <div class="timeline-item">
                      <div class="timeline-date">{{ $activity->created_at->format('Y/m/d H:i') }}</div>
                      <div>{{ $activity->message }}</div>
                    </div>
                  @endforeach
                --}}
              </div>
            </section>
          </div>

          {{-- ============================================================
              サイドバー（右側）
              ダッシュボード統計とアクションボタン
              
              sticky クラスでスクロール時に固定表示
          ============================================================ --}}
          <aside class="sticky">
            <div class="panel" style="display: grid; gap: 12px">
              <h2>ダッシュボード</h2>
              
              {{-- 
                統計情報（将来実装予定）
                現在はプレースホルダーとして「-」を表示
              --}}
              <div style="display: flex; justify-content: space-between; font-size: 14px">
                <span>累計閲覧数</span>
                <strong>-</strong>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 14px">
                <span>今月の問い合わせ</span>
                <strong>-</strong>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 14px">
                <span>現在のいいね数</span>
                <strong>-</strong>
              </div>
              
              {{-- 
                アクションボタン
                route() でルート名からURLを生成
                
                【将来実装予定】
                「公開設定を変更」ボタン:
                - 公開中(STATUS=1)の土地をland_public画面で編集可能
                - land_public画面でSTATUSを0に変更すると非公開になり、land_public画面にリロード
              --}}
              <a href="{{ route('my_land_list') }}" class="btn btn-secondary" style="justify-content: center">土地一覧に戻る</a>
              <a href="{{ route('land_public', $land->LAND_ID) }}" class="btn btn-primary" style="justify-content: center">公開設定を変更</a>
            </div>
          </aside>
        </div>
      </section>
    </main>
@endsection

{{-- ============================================================
    4. このページ専用のCSS
============================================================ --}}
@push('styles')
<style>
  /* メインコンテンツのパディング */
  main {
    padding: 40px 0 60px;
  }
  
  /* ページヘッダー */
  .page-header {
    margin-bottom: 28px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 12px;
  }
  .page-header h1 {
    font-size: 26px;
    font-weight: 700;
    color: #222;
  }
  
  /* ステータスバッジ */
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #e8f5e9;
    color: #2e7d32;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
  }
  
  /* 2カラムレイアウト */
  .layout {
    display: grid;
    grid-template-columns: 2fr 1fr; /* メイン:サイド = 2:1 */
    gap: 24px;
  }
  
  /* パネル共通スタイル */
  .panel {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 24px;
  }
  .panel h2 {
    font-size: 18px;
    font-weight: 600;
    color: #222;
    margin-bottom: 16px;
  }
  
  /* 情報テーブル */
  .info-table {
    width: 100%;
    border-collapse: collapse;
  }
  .info-table th,
  .info-table td {
    text-align: left;
    padding: 10px 0;
    border-bottom: 1px dashed #e0e0e0;
    font-size: 14px;
  }
  .info-table th {
    width: 160px;
    color: #666;
    font-weight: 500;
  }
  
  /* 予約リスト */
  .reservation-list {
    display: grid;
    gap: 16px;
  }
  
  /* タイムライン */
  .timeline {
    display: grid;
    gap: 12px;
  }
  
  /* スティッキーサイドバー */
  .sticky {
    position: sticky;
    top: 88px; /* ヘッダー高さ(60px) + 余白 */
    display: grid;
    gap: 20px;
  }
  
  /* ボタン共通スタイル */
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
  
  /* レスポンシブ対応（960px以下） */
  @media (max-width: 960px) {
    .layout {
      grid-template-columns: 1fr; /* 1カラムに変更 */
    }
    .sticky {
      position: static; /* 固定を解除 */
    }
  }
</style>
@endpush
