{{--
============================================================
空状態 コンポーネント (empty-state.blade.php)
============================================================

【このコンポーネントの役割】
  - データがない場合の表示（汎用）

【受け取るプロパティ】
  - $icon: 表示するアイコン（絵文字推奨）
  - $title: タイトルテキスト
  - $message: メッセージテキスト
  - $link: アクションボタンのURL
  - $linkText: アクションボタンのテキスト

============================================================
--}}

<div class="empty-state">
    <div class="empty-icon">{{ $icon }}</div>
    <h3 class="empty-title">{{ $title }}</h3>
    <p class="empty-text">{{ $message }}</p>
    @if(isset($link) && isset($linkText))
        <a href="{{ $link }}" class="btn btn-primary">{{ $linkText }}</a>
    @endif
</div>
