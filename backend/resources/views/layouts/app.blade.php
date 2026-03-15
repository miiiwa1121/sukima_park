<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'スキマパーク') - スキマパーク</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- アプリケーションCSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    {{-- ヘッダー --}}
    @include('layouts.header')
    
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
    
    {{-- フッター --}}
    @if(!isset($hideFooter) || !$hideFooter)
        @include('layouts.footer')
    @endif
    
    {{-- JavaScript --}}
    @stack('scripts')
</body>
</html>
