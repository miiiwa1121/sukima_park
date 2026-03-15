@extends('layouts.app')

@section('title', 'レイアウト確認用（テスト）')

@section('content')
    <h1>レイアウト確認用ページ</h1>
    <p>このページは共通レイアウトの確認用です。後で削除してください。</p>
    
    <hr style="margin: 40px 0;">
    
    {{-- カードの例 --}}
    <h2>カードコンポーネント</h2>
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3 style="margin: 0;">カードタイトル</h3>
        </div>
        <div class="card-body">
            <p>これはカードのコンテンツです。土地の情報などを表示できます。</p>
            <p>面積: 100㎡ / 価格: 500円/日</p>
        </div>
        <div class="card-footer">
            <a href="#" class="btn btn-primary">詳細を見る</a>
            <a href="#" class="btn btn-outline">お気に入り</a>
        </div>
    </div>
    
    <hr style="margin: 40px 0;">
    
    {{-- ボタンの例 --}}
    <h2>ボタン</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
        <button class="btn btn-primary">Primary Button</button>
        <button class="btn btn-secondary">Secondary Button</button>
        <button class="btn btn-outline">Outline Button</button>
    </div>
    
    <hr style="margin: 40px 0;">
    
    {{-- フォームの例 --}}
    <h2>フォーム</h2>
    <div class="card">
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label class="form-label required">市区町村</label>
                    <input type="text" class="form-input" placeholder="例: 新宿区">
                </div>
                
                <div class="form-group">
                    <label class="form-label">都道府県</label>
                    <select class="form-select">
                        <option value="">選択してください</option>
                        <option value="12">東京都</option>
                        <option value="27">大阪府</option>
                        <option value="14">神奈川県</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">説明</label>
                    <textarea class="form-textarea" placeholder="土地の説明を入力してください"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">送信する</button>
            </form>
        </div>
    </div>
    
    <hr style="margin: 40px 0;">
    
    {{-- アラートの例 --}}
    <h2>アラート</h2>
    <div class="alert alert-success">
        ✅ 成功しました！土地が登録されました。
    </div>
    <div class="alert alert-error">
        ❌ エラーが発生しました。入力内容を確認してください。
    </div>
@endsection
