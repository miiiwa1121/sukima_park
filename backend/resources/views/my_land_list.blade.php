{{--
============================================================
自己保持土地一覧画面 (my_land_list.blade.php)
============================================================

【画面の概要】
  自分が所有している土地の一覧を表示する画面です。
  公開中・非公開の両方の土地を確認でき、フィルタリングも可能です。

【対応画面定義】
  - context/画面一覧/my_land_list.csv
  - context/画面レイアウト/my_lands_list_screen.html（HTMLテンプレート）

【このファイルの役割】
  1. ログインユーザーの全土地を表示（公開・非公開両方）
  2. ステータスでフィルタリング（すべて/公開中/非公開）
  3. 各土地のアクションボタン表示
     - 公開中（STATUS=1）→ 貸出中詳細へ
     - 非公開（STATUS=0）→ 貸出条件を設定

【LandControllerから受け取るデータ】
  - $lands: ログインユーザーの土地コレクション（Landモデルの配列）
    ※ LandController@myList で Auth::id() を使って取得
  - $currentStatus: 現在のフィルタ状態（'all', '0', '1'）
  - $prefectures: 都道府県コードと名前の対応配列

【画面項目定義との対応】
  No.1  「自己保持土地一覧」タイトル → <h1>
  No.2  説明文 → <p>
  No.3-5 フィルタボタン（すべて/公開中/非公開） → .filter-chip
  No.6  土地カード（繰り返し） → @forelse ループ
  No.7  プレビュー画像 → .land-thumb
  No.8  土地名 → <h2>
  No.9  ステータス → .status.published / .status.private
  No.11 住所表示 → .land-meta 内
  No.12 面積 → .land-meta 内
  No.13 説明 → .land-desc
  No.14 詳細画面遷移ボタン → .land-actions 内のボタン

【使用しているBlade機能】
  - @extends: layouts.appを継承してヘッダー・フッターを自動表示
  - @section: ページタイトルとメインコンテンツを定義
  - @forelse/@empty: 土地がある場合はカード表示、ない場合は「登録されていません」
  - {{ }}: 変数の表示（XSSエスケープ済み）
  - route(): ルート名からURLを生成
  - @push('styles'): このページ専用のCSSを追加

============================================================
--}}

{{-- ============================================================
    1. レイアウトの継承
    layouts/app.blade.php を継承することで、
    ヘッダーとフッターが自動的に表示されます
============================================================ --}}
@extends('layouts.app')

{{-- ============================================================
    2. ページタイトルの設定
    ブラウザのタブに表示されるタイトルです
============================================================ --}}
@section('title', '自己保持土地一覧 - スキマパーク')

{{-- ============================================================
    3. メインコンテンツ
    layouts/app.blade.php の @yield('content') 部分に挿入されます
============================================================ --}}
@section('content')
    <main>
      <section class="container">
        
        {{-- ============================================================
            ページヘッダー
            画面タイトルと説明文、フィルタボタンを表示
        ============================================================ --}}
        <div class="page-header">
          <div>
            {{-- 画面項目No.1: 自己保持土地一覧（タイトル） --}}
            <h1>自己保持土地一覧</h1>
            
            {{-- 画面項目No.2: 説明文 --}}
            <p style="color: #555; margin-top: 8px">
              公開・非公開のすべての土地が確認できます。ステータスに応じて
              貸出条件の編集や貸出状況の確認を行ってください。
            </p>
          </div>
          
          {{-- ============================================================
              フィルタボタン（画面項目No.3-5）
              
              route('my_land_list', ['status' => '値']) で
              /my_land_list?status=all のようなURLを生成
              
              $currentStatus と比較して active クラスを追加
          ============================================================ --}}
          <div class="filters">
            {{-- 画面項目No.3: すべて（STATUS不問） --}}
            <a href="{{ route('my_land_list', ['status' => 'all']) }}" 
               class="filter-chip {{ $currentStatus === 'all' ? 'active' : '' }}">すべて</a>
            
            {{-- 画面項目No.4: 公開中（STATUS=1） --}}
            <a href="{{ route('my_land_list', ['status' => '1']) }}" 
               class="filter-chip {{ $currentStatus === '1' ? 'active' : '' }}">公開中</a>
            
            {{-- 画面項目No.5: 非公開（STATUS=0） --}}
            <a href="{{ route('my_land_list', ['status' => '0']) }}" 
               class="filter-chip {{ $currentStatus === '0' ? 'active' : '' }}">非公開</a>
          </div>
        </div>

        {{-- ============================================================
            土地リスト（画面項目No.6: 土地カード繰り返し）
            
            @forelse: $lands に土地がある場合はループ表示
            @empty: 土地がない場合のメッセージを表示
            
            $lands は LandController@myList で取得した
            ログインユーザーの土地コレクションです
        ============================================================ --}}
        <div class="land-list">
          @forelse($lands as $land)
            {{-- ============================================================
                土地カード 1件分
                
                $land は Landモデルのインスタンス
                主要なプロパティ:
                  - LAND_ID: 土地ID（主キー）
                  - NAME: 土地名（NULL可）
                  - PEREFECTURES: 都道府県コード（1-47）
                  - CITY: 市区町村
                  - STREET_ADDRESS: 番地
                  - AREA: 面積（㎡）
                  - DESCRIPTION: 説明文
                  - IMAGE: 画像パス
                  - STATUS: 公開状態（0=非公開, 1=公開中）
                  - PRICE: 賃料
                  - PRICE_UNIT: 料金単位（0=日, 1=時間, 2=15分）
            ============================================================ --}}
            <div class="land-card">
              {{-- 画面項目No.7: プレビュー画像 --}}
              <div class="land-thumb">
                @if($land->IMAGE)
                  {{-- 
                    asset('storage/' . $land->IMAGE) で
                    public/storage/以下の画像パスを生成
                  --}}
                  <img src="{{ asset('storage/' . $land->IMAGE) }}" alt="プレビュー" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                @else
                  {{-- 画像がない場合はプレースホルダー表示 --}}
                  プレビュー
                @endif
              </div>

              <div class="land-body">
                {{-- タイトルとステータスバッジ --}}
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                  {{-- 
                    画面項目No.8: 土地名
                    NAMEがNULLの場合は「市区町村 + 番地」を代わりに表示
                    ?? は Null合体演算子（左辺がNULLなら右辺を使用）
                  --}}
                  <h2>{{ $land->NAME ?? ($land->CITY . $land->STREET_ADDRESS) }}</h2>
                  
                  {{-- 
                    画面項目No.9: ステータス
                    STATUS=1（公開中）→ 緑色のバッジ
                    STATUS=0（非公開）→ 赤色のバッジ
                  --}}
                  @if($land->STATUS == 1)
                    <span class="status published">公開中</span>
                  @else
                    <span class="status private">非公開</span>
                  @endif
                </div>

                {{-- 
                  画面項目No.11,12: メタ情報（住所・面積・更新日）
                  
                  $prefectures[$land->PEREFECTURES] で
                  都道府県コード（数字）を都道府県名（文字列）に変換
                  例: 13 → '東京都'
                --}}
                <div class="land-meta">
                  <span>{{ $prefectures[$land->PEREFECTURES] ?? '' }}・{{ $land->CITY }}</span>
                  <span>面積: {{ $land->AREA ?? '-' }}㎡</span>
                  @if($land->updated_at)
                    {{-- 
                      updated_at は Carbon インスタンス
                      format('Y/m/d') で "2026/01/07" 形式に変換
                    --}}
                    <span>更新日: {{ $land->updated_at->format('Y/m/d') }}</span>
                  @endif
                </div>

                {{-- 
                  画面項目No.13: 説明
                  Str::limit() で60文字に切り詰め
                  DESCRIPTIONがNULLの場合は「説明はありません」を表示
                --}}
                <p class="land-desc">
                  {{ Str::limit($land->DESCRIPTION ?? '説明はありません', 60) }}
                </p>

                {{-- 
                  画面項目No.14: アクションボタン
                  
                  STATUSによって遷移先が変わる（my_land_list.csv 参照）
                  - STATUS=0（非公開）→ land_public.php（貸出条件設定）
                  - STATUS=1（公開中）→ loan_detail.php（貸出中詳細）
                  
                  route('ルート名', パラメータ) でURLを生成
                  例: route('loan_detail', 5) → /loan_detail/5
                --}}
                <div class="land-actions">
                  @if($land->STATUS == 1)
                    {{-- 公開中の土地 → 貸出中詳細画面へ --}}
                    <a href="{{ route('loan_detail', $land->LAND_ID) }}" class="btn btn-primary">貸出中詳細へ</a>
                  @else
                    {{-- 非公開の土地 → 貸出条件設定画面へ --}}
                    <a href="{{ route('land_public', $land->LAND_ID) }}" class="btn btn-primary">貸出条件を設定</a>
                  @endif
                  {{-- どちらのステータスでも土地詳細確認は可能 --}}
                  <a href="{{ route('land_public', $land->LAND_ID) }}" class="btn btn-secondary">土地詳細を確認</a>
                </div>
              </div>
            </div>
          @empty
            {{-- ============================================================
                土地がない場合の表示
                
                $lands が空（ユーザーが土地を所有していない）場合に
                このブロックが表示されます
            ============================================================ --}}
            <p style="text-align: center; color: #666; padding: 60px 20px;">
              土地が登録されていません。<br>
              <a href="#" class="btn btn-primary" style="margin-top: 16px;">土地を登録する</a>
            </p>
          @endforelse
        </div>
      </section>
    </main>
@endsection

{{-- ============================================================
    4. このページ専用のCSS
    
    @push('styles') を使うと、
    layouts/app.blade.php の @stack('styles') 部分に挿入されます
    
    共通CSSは layouts/app.blade.php にあり、
    このページ固有のスタイルのみここに記述します
============================================================ --}}
@push('styles')
<style>
  /* メインコンテンツのパディング */
  main {
    padding: 40px 0 60px;
  }
  
  /* ページヘッダー（タイトル + フィルタ） */
  .page-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
    margin-bottom: 32px;
  }
  .page-header h1 {
    font-size: 26px;
    font-weight: 700;
    color: #222;
  }
  
  /* フィルタボタン群 */
  .filters {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }
  .filter-chip {
    padding: 6px 14px;
    border-radius: 999px;
    background: #f1f8e9;
    border: 1px solid #c5e1a5;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    color: #333;
  }
  .filter-chip.active,
  .filter-chip:hover {
    background: #c5e1a5;
    color: #1b5e20;
  }
  
  /* 土地カードリスト */
  .land-list {
    display: grid;
    gap: 20px;
  }
  .land-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 20px;
    display: grid;
    grid-template-columns: 160px 1fr; /* サムネイル | 本文 */
    gap: 20px;
  }
  
  /* サムネイル部分 */
  .land-thumb {
    width: 160px;
    height: 120px;
    background: #e0e0e0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 13px;
    overflow: hidden;
  }
  
  /* カード本文 */
  .land-body {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .land-body h2 {
    font-size: 18px;
    font-weight: 600;
    color: #222;
  }
  
  /* メタ情報（住所・面積・更新日） */
  .land-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    color: #666;
    font-size: 13px;
  }
  
  /* 説明文 */
  .land-desc {
    color: #555;
    font-size: 14px;
  }
  
  /* アクションボタン群 */
  .land-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }
  
  /* ステータスバッジ */
  .status {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }
  .status.published {
    background: #e8f5e9;
    color: #2e7d32;
  }
  .status.private {
    background: #ffebee;
    color: #c62828;
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
  
  /* レスポンシブ対応（768px以下） */
  @media (max-width: 768px) {
    .land-card {
      grid-template-columns: 1fr; /* 縦並びに変更 */
    }
    .land-thumb {
      width: 100%;
    }
    .land-actions {
      flex-direction: column;
      align-items: stretch;
    }
  }
</style>
@endpush
