{{--
============================================================
管理者用レイアウト (admin.blade.php)
============================================================

【このファイルの役割】
- 管理者画面専用のレイアウトテンプレート
- user_list, user_detail, contact_list, contact_detail で使用

【構成】
1. 管理者用ヘッダー（admin_header.blade.php）
2. メインコンテンツ
3. フラッシュメッセージ

============================================================
--}}

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', '管理画面') - スキマパーク</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- アプリケーションCSS（ユーザ用と共通のスタイルを使用） -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  @stack('styles')
</head>

<body>
  {{-- 管理者用ヘッダー --}}
  @include('layouts.admin_header')

  {{-- フラッシュメッセージ --}}
  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-error">
      {{ session('error') }}
    </div>
  @endif

  {{-- メインコンテンツ --}}
  <main class="main-content">
    <div class="container">
      @yield('content')
    </div>
  </main>

  {{-- JavaScript --}}
  @stack('scripts')
</body>

</html>