{{--
============================================================
土地貸出設定画面 (land_public.blade.php)
============================================================

【画面の概要】
  土地の貸出条件を編集する画面です。
  非公開の土地を公開したり、料金や説明を変更できます。

【対応画面定義】
  - context/画面一覧/land_public.csv
  - context/画面レイアウト/listed_lands_screen.html（HTMLテンプレート）

【このファイルの役割】
  1. 土地情報の編集フォームを表示
  2. 公開ステータスの切り替えUI
  3. 入力内容のプレビュー表示

【LandControllerから受け取るデータ】
  - $land: 編集対象の土地（Landモデル）
    ※ LandController@edit で LAND_ID をもとに取得
    ※ 所有者チェック済み（自分の土地のみ編集可能）
  - $prefectures: 都道府県コードと名前の対応配列

【画面項目定義との対応（land_public.csv）】
  No.5  土地名入力 → input[name="NAME"]
  No.8  都道府県入力 → select[name="PEREFECTURES"]
  No.10 市区町村入力 → input[name="CITY"]
  No.12 番地入力 → input[name="STREET_ADDRESS"]
  No.14 面積入力 → input[name="AREA"]
  No.15 写真 → .upload-box（将来実装）
  No.23 賃料入力 → input[name="PRICE"]
  No.24 料金単位 → select[name="PRICE_UNIT"]
  No.26 詳細説明 → textarea[name="DESCRIPTION"]
  No.27 保存ボタン → button[type="submit"]（将来実装）
  No.28 公開ステータス変更 → サイドバー内（将来実装）

【フォームの仕組み】
  1. 初期値: old() で前回入力値、なければ $land の現在値を表示
  2. バリデーション: 将来実装（LandControllerのupdateメソッド）
  3. 保存処理: 将来実装（POSTリクエスト）

【使用しているBlade機能】
  - @extends: layouts.appを継承
  - @section: ページタイトルとメインコンテンツを定義
  - {{ old('name', $default) }}: フォームの初期値設定
  - @foreach: 都道府県選択肢のループ表示
  - @csrf: CSRFトークン生成（セキュリティ対策）
  - @push('styles'): このページ専用のCSS

============================================================
--}}

{{-- ============================================================
    1. レイアウトの継承
============================================================ --}}
@extends('layouts.app')

{{-- ============================================================
    2. ページタイトルの設定
============================================================ --}}
@section('title', '土地貸出設定 - スキマパーク')

{{-- ============================================================
    3. メインコンテンツ
============================================================ --}}
@section('content')
    <main>
      <section class="container">
        
        {{-- ============================================================
            ページヘッダー
        ============================================================ --}}
        <div class="page-header">
          <h1>土地貸出設定</h1>
          <p style="color: #555; margin-top: 8px">
            公開前に必要な情報を入力し、貸出条件を確定してください。保存後に公開
            ステータスを「公開」に変更すると募集が開始されます。
          </p>
        </div>

        {{-- ============================================================
            2カラムレイアウト
            左: 編集フォーム
            右: サイドバー（公開ステータス、プレビュー）
        ============================================================ --}}
        <div class="layout">
          
          {{-- ============================================================
              編集フォーム（左側）
              
              method="POST" で送信（将来実装）
              @csrf でCSRFトークンを自動生成
              
              【セキュリティ】
              @csrf は Laravel のクロスサイトリクエストフォージェリ対策
              これがないとフォーム送信時に419エラーになる
          ============================================================ --}}
          <form class="panel" style="display: grid; gap: 24px" method="POST" action="#">
            @csrf
            
            {{-- ============================================================
                基本情報セクション
            ============================================================ --}}
            <section>
              <h2>基本情報</h2>
              
              {{-- 
                画面項目No.5: 土地名入力
                
                old('NAME', $land->NAME) の意味:
                1. バリデーションエラーで戻ってきた場合 → 入力していた値を復元
                2. 初回表示の場合 → $land->NAME（DBの値）を表示
              --}}
              <div class="form-group">
                <label>土地名</label>
                <input type="text" name="NAME" class="form-control" 
                       value="{{ old('NAME', $land->NAME) }}"
                       placeholder="例: 都市型ポップアップスペース">
              </div>

              {{-- 
                画面項目No.8: 都道府県入力
                
                @foreach で $prefectures をループ
                selected 属性で現在の値を選択状態に
              --}}
              <div class="form-group">
                <label>都道府県</label>
                <select name="PEREFECTURES" class="form-control">
                  @foreach($prefectures as $code => $name)
                    {{-- 
                      $land->PEREFECTURES == $code の場合に selected を追加
                      三項演算子: 条件 ? 真の値 : 偽の値
                    --}}
                    <option value="{{ $code }}" {{ $land->PEREFECTURES == $code ? 'selected' : '' }}>
                      {{ $name }}
                    </option>
                  @endforeach
                </select>
              </div>

              {{-- 画面項目No.10: 市区町村入力 --}}
              <div class="form-group">
                <label>市区町村</label>
                <input type="text" name="CITY" class="form-control" 
                       value="{{ old('CITY', $land->CITY) }}"
                       placeholder="例: 中央区">
              </div>

              {{-- 画面項目No.12: 番地入力 --}}
              <div class="form-group">
                <label>番地</label>
                <input type="text" name="STREET_ADDRESS" class="form-control" 
                       value="{{ old('STREET_ADDRESS', $land->STREET_ADDRESS) }}"
                       placeholder="例: 日本橋1-2-3">
                <p class="hint">町名・番地・建物名まで入力してください。</p>
              </div>

              {{-- 画面項目No.14: 面積入力 --}}
              <div class="grid-two">
                <div class="form-group">
                  <label>面積</label>
                  <input type="text" name="AREA" class="form-control" 
                         value="{{ old('AREA', $land->AREA) }}"
                         placeholder="例: 80">
                </div>
              </div>

              {{-- 
                画面項目No.15: 写真
                
                【将来実装予定】
                - ファイルアップロード機能
                - 画像プレビュー
                
                ※ 1つの土地につき写真は1枚のみ（IMAGEカラム）
              --}}
              <div class="form-group">
                <label>写真</label>
                <div class="upload-box">
                  画像をドラッグ＆ドロップするか、クリックしてアップロード
                </div>
                <p class="hint">推奨: 横向き写真 / 1枚のみ / 5MB以内</p>
              </div>
            </section>

            {{-- ============================================================
                貸出条件セクション
            ============================================================ --}}
            <section>
              <h2>貸出条件</h2>
              <div class="grid-two">
                {{-- 画面項目No.23: 賃料入力 --}}
                <div class="form-group">
                  <label>賃料（税抜）</label>
                  <input type="text" name="PRICE" class="form-control" 
                         value="{{ old('PRICE', $land->PRICE) }}"
                         placeholder="例: 20000">
                </div>
                
                {{-- 
                  画面項目No.24: 料金単位
                  PRICE_UNIT: 0=日あたり, 1=時間あたり, 2=15分あたり
                --}}
                <div class="form-group">
                  <label>料金単位</label>
                  <select name="PRICE_UNIT" class="form-control">
                    <option value="0" {{ $land->PRICE_UNIT == 0 ? 'selected' : '' }}>日あたり</option>
                    <option value="1" {{ $land->PRICE_UNIT == 1 ? 'selected' : '' }}>時間あたり</option>
                    <option value="2" {{ $land->PRICE_UNIT == 2 ? 'selected' : '' }}>15分あたり</option>
                  </select>
                </div>
              </div>
            </section>

            {{-- ============================================================
                説明セクション
            ============================================================ --}}
            <section>
              <h2>説明</h2>
              {{-- 画面項目No.26: 詳細説明 --}}
              <div class="form-group">
                <label>詳細説明</label>
                {{-- 
                  textarea の値は開始タグと終了タグの間に記述
                  ※ input要素と異なり value 属性は使えない
                --}}
                <textarea name="DESCRIPTION" class="form-control" 
                          placeholder="利用想定や禁止事項などを詳しく記載してください。"
                          style="min-height: 160px">{{ old('DESCRIPTION', $land->DESCRIPTION) }}</textarea>
              </div>
            </section>

            {{-- ============================================================
                ボタンセクション
                
                【将来実装予定】
                - 保存ボタン: フォームデータをPOST送信
                - バリデーション処理
                - 成功/エラーメッセージ表示
            ============================================================ --}}
            <section style="display: flex; justify-content: flex-end; gap: 12px">
              <a href="{{ route('my_land_list') }}" class="btn btn-secondary">キャンセル</a>
              <button type="submit" class="btn btn-primary">保存（未実装）</button>
            </section>
          </form>

          {{-- ============================================================
              サイドバー（右側）
              公開ステータス切り替えとプレビュー
          ============================================================ --}}
          <aside class="panel sticky" style="display: grid; gap: 20px">
            
            {{-- 
              公開ステータス切り替えボックス
              
              フォームでPOSTリクエストを送信してステータスを切り替える
              route('land_public.toggle_status', $land->LAND_ID) へPOST送信
            --}}
            <section class="status-toggle">
              <strong>公開ステータス: {{ $land->STATUS == 1 ? '公開中' : '非公開' }}</strong>
              <p style="font-size: 13px; color: #2e7d32">
                @if($land->STATUS == 0)
                  ステータスを「公開」に変更すると公開ページが作成され、募集が開始されます。
                @else
                  現在公開中です。非公開にすると募集が停止されます。
                @endif
              </p>
              {{-- 
                ステータス切り替えフォーム
                POST送信後のリダイレクト:
                - 非公開→公開: loan_detail画面へ
                - 公開→非公開: land_public画面リロード
              --}}
              <form method="POST" action="{{ route('land_public.toggle_status', $land->LAND_ID) }}">
                @csrf
                <button type="submit" class="btn btn-primary" style="justify-content: center; width: 100%;">
                  {{ $land->STATUS == 0 ? '公開に切り替える' : '非公開に切り替える' }}
                </button>
              </form>
            </section>

            {{-- 
              プレビューセクション
              フォームに入力された内容をリアルタイムで表示（将来実装）
              現在はDBの値を静的に表示
            --}}
            <section>
              <h2>公開プレビュー</h2>
              <div class="summary">
                <div class="summary-item">
                  <span>土地名</span>
                  <span>{{ $land->NAME ?? '未入力' }}</span>
                </div>
                <div class="summary-item">
                  <span>所在地</span>
                  <span>{{ $prefectures[$land->PEREFECTURES] ?? '' }}{{ $land->CITY ?? '未入力' }}</span>
                </div>
                <div class="summary-item">
                  <span>料金</span>
                  <span>
                    @if($land->PRICE)
                      ¥{{ number_format($land->PRICE) }}
                    @else
                      未入力
                    @endif
                  </span>
                </div>
              </div>
            </section>
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
    margin-bottom: 32px;
  }
  .page-header h1 {
    font-size: 26px;
    font-weight: 700;
    color: #222;
  }
  
  /* 2カラムレイアウト */
  .layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
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
  
  /* フォームグループ */
  .form-group {
    margin-bottom: 18px;
  }
  .form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #444;
  }
  
  /* フォームコントロール（入力欄共通） */
  .form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
  }
  .form-control:focus {
    outline: none;
    border-color: #2e7d32;
    box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.1);
  }
  textarea.form-control {
    min-height: 120px;
    resize: vertical;
  }
  
  /* 2列グリッド */
  .grid-two {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }
  
  /* ヒントテキスト */
  .hint {
    color: #777;
    font-size: 12px;
    margin-top: 6px;
  }
  
  /* アップロードボックス */
  .upload-box {
    border: 2px dashed #c5e1a5;
    border-radius: 8px;
    padding: 18px;
    text-align: center;
    color: #2e7d32;
    background: #f1f8e9;
    font-size: 14px;
  }
  
  /* ステータス切り替えボックス */
  .status-toggle {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    border-radius: 8px;
    background: #e8f5e9;
    border: 1px solid #c5e1a5;
  }
  .status-toggle strong {
    color: #1b5e20;
    font-size: 14px;
  }
  
  /* プレビューサマリー */
  .summary {
    display: grid;
    gap: 12px;
    font-size: 14px;
  }
  .summary-item {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px dashed #e0e0e0;
    padding-bottom: 6px;
  }
  
  /* スティッキーサイドバー */
  .sticky {
    position: sticky;
    top: 88px;
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
  .btn-primary:disabled {
    background: #a5d6a7;
    cursor: not-allowed;
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
      grid-template-columns: 1fr;
    }
    .sticky {
      position: static;
    }
  }
</style>
@endpush
