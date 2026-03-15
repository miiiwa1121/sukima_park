{{--
============================================================
取引完了一覧画面 (trade_list.blade.php)
============================================================

【対応画面定義】
  - 取引完了一覧

【このファイルの役割】
  - ログインユーザーが完了した取引の一覧を表示

【受け取るデータ】
  - $trades: 完了した取引のコレクション
    → コントローラーから渡される

============================================================
--}}

@extends('layouts.app')

@section('title', '取引完了一覧 - スキマパーク')

@section('content')
<div class="page-header">
    <h1 class="page-title">取引完了一覧</h1>
    <p class="page-subtitle">過去に利用した土地の一覧です</p>
</div>

<section class="section">
    <div class="rental-list">
        @forelse($trades as $trade)
            <x-trade-card :trade="$trade" />
        @empty
            <x-empty-state
                icon="📋"
                title="過去の取引完了一覧はありません"
                message="土地をレンタルすると、こちらに履歴が表示されます"
                link="{{ route('home') }}"
                linkText="土地を探す"
            />
        @endforelse
    </div>
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endpush
