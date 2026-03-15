{{--
============================================================
レンタル中の土地一覧画面 (rental_list.blade.php)
============================================================

【対応画面定義】
  - レンタル中の土地一覧

【このファイルの役割】
  - ログインユーザーが現在借りている土地の一覧を表示

【受け取るデータ】
  - $rentals: レンタル中の土地のコレクション
    → RentalController@index から渡される
    → RentalRecordモデルのコレクション（landリレーション含む）

============================================================
--}}

@extends('layouts.app')

@section('title', 'レンタル中の土地 - スキマパーク')

@section('content')
<div class="page-header">
    <h1 class="page-title">レンタル中の土地</h1>
    <p class="page-subtitle">現在借りている土地の一覧です</p>
</div>

<section class="section">
    <div class="rental-list">
        @forelse($rentals as $rental)
            <x-rental-card :rental="$rental" :detailRoute="$detailRoute ?? 'rental_list.show'" />
        @empty
            <x-empty-state
                icon="📦"
                title="現在レンタル中の土地はありません"
                message="気になる土地を見つけて、レンタルを開始しましょう"
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
