{{--
============================================================
管理者用ヘッダー (admin_header.blade.php)
============================================================

【このファイルの役割】
- 管理者画面専用のヘッダー
- user_list, user_detail, contact_list, contact_detail で使用

【構成要素】
1. ロゴ（スキマパーク 管理画面） → クリックでユーザ一覧へ遷移
2. 問い合わせ一覧リンク
3. ログアウトボタン

============================================================
--}}

<header class="header">
  <div class="header-container">
    {{-- 左側：ロゴ（ユーザ一覧へ） --}}
    <a href="{{ url('/admin/users') }}" class="logo">
      <span class="logo-text">スキマパーク (管理画面)</span>
    </a>

    {{-- 右側：ナビゲーション --}}
    <nav class="nav">
      <ul class="nav-list">
        {{-- 問い合わせ一覧リンク --}}
        <li>
          <a href="{{ url('/admin/contact_list') }}" class="nav-link">
            問い合わせ一覧
          </a>
        </li>

        {{-- ログアウトボタン --}}
        <li>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-icon-btn" title="ログアウト">🚪</button>
          </form>
        </li>
      </ul>
    </nav>
  </div>
</header>