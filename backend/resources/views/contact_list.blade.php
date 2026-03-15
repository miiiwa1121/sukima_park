{{--
============================================================
問い合わせ一覧画面 (contact_list.blade.php)
============================================================

【対応画面定義】
  - 管理画面 - 問い合わせ一覧

【このファイルの役割】
  - 管理者向け問い合わせ一覧の表示
  - 問い合わせの検索・フィルタリング

【受け取るデータ】
  - $contacts: 問い合わせデータのコレクション
    - CONTACT_ID: 問い合わせID
    - TITLE: 件名
    - MESSAGE: 問い合わせ内容
    - DATE: 問い合わせ日
    - STATUS: ステータス (0=新規, 1=対応中, 2=完了)
    - sender: 送信者（MEMBER_TABLE）

【画面構成】
  1. ヘッダー（管理者用共通ヘッダー）
  2. フィルターボックス
  3. 問い合わせ一覧テーブル

============================================================
--}}

{{-- 管理者用レイアウトを継承 --}}
@extends('layouts.admin')

{{-- ページタイトル --}}
@section('title', '問い合わせ一覧')

{{-- メインコンテンツ --}}
@section('content')
    <div class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">問い合わせ一覧</h2>
            </div>

            <div class="filter-box">
                <form class="filter-form" method="GET" action="{{ url('/admin/contact_list') }}">
                    <div class="filter-group" style="grid-column: 1 / span 2">
                        <label for="keyword">件名 or 内容</label>
                        <input
                            type="text"
                            id="keyword"
                            name="keyword"
                            class="form-control"
                            placeholder="例: 料金, 登録方法"
                            value="{{ request('keyword') }}"
                        />
                    </div>
                    <div class="filter-group">
                        <label for="user_email">ユーザーEメール</label>
                        <input
                            type="text"
                            id="user_email"
                            name="user_email"
                            class="form-control"
                            value="{{ request('user_email') }}"
                        />
                    </div>
                    <div class="filter-group">
                        <label for="status">ステータス</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">すべて</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>新規</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>対応中</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>完了</option>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">絞り込む</button>
                </form>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>日付</th>
                            <th>ユーザー名</th>
                            <th>件名</th>
                            <th>ステータス</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- 問い合わせデータをループして表示 --}}
                        @forelse($contacts ?? [] as $contact)
                            <tr>
                                <td>{{ $contact->CONTACT_ID }}</td>
                                <td>{{ $contact->DATE?->format('Y-m-d') ?? $contact->DATE }}</td>
                                <td>{{ $contact->sender->NAME ?? $contact->sender->USERNAME ?? '不明' }}</td>
                                <td>{{ $contact->TITLE }}</td>
                                <td>
                                    @switch($contact->STATUS)
                                        @case(0)
                                            <span class="status-badge status-new">新規</span>
                                            @break
                                        @case(1)
                                            <span class="status-badge status-open">対応中</span>
                                            @break
                                        @case(2)
                                            <span class="status-badge status-closed">完了</span>
                                            @break
                                        @default
                                            <span class="status-badge">{{ $contact->STATUS }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ url('/admin/contact/' . $contact->CONTACT_ID) }}" class="btn btn-secondary">詳細</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                    問い合わせデータがありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            @if($contacts->hasPages())
                <div class="pagination-wrapper">
                    <nav class="simple-pagination">
                        {{-- 前へ --}}
                        @if($contacts->onFirstPage())
                            <span class="page-arrow disabled">←</span>
                        @else
                            <a href="{{ $contacts->previousPageUrl() }}" class="page-arrow">←</a>
                        @endif

                        {{-- ページ番号 --}}
                        <span class="page-info">{{ $contacts->currentPage() }} / {{ $contacts->lastPage() }}</span>

                        {{-- 次へ --}}
                        @if($contacts->hasMorePages())
                            <a href="{{ $contacts->appends(request()->query())->nextPageUrl() }}" class="page-arrow">→</a>
                        @else
                            <span class="page-arrow disabled">→</span>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .section {
        padding: 40px 0;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #222;
    }

    .view-all {
        color: #2e7d32;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .filter-box {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 32px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        align-items: flex-end;
    }

    .filter-group {
        margin-bottom: 0;
        text-align: left;
    }

    .filter-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #555;
    }

    .filter-btn {
        width: 100%;
        padding: 10px 12px;
        background: #2e7d32;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .filter-btn:hover {
        background: #1b5e20;
    }

    /* 問い合わせ一覧用のテーブルスタイル */
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
        font-size: 14px;
    }

    .data-table th {
        background: #f9f9f9;
        font-weight: 600;
        color: #555;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover {
        background: #f5f5f5;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-new {
        background: #fff8e1;
        color: #f57f17;
    }

    .status-open {
        background: #e3f2fd;
        color: #1565c0;
    }

    .status-closed {
        background: #f1f8e9;
        color: #33691e;
    }

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

    .btn-secondary {
        background: #f5f5f5;
        color: #333;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    .simple-pagination {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .page-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        font-size: 18px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        color: #2e7d32;
        text-decoration: none;
        transition: all 0.2s;
    }

    .page-arrow:hover:not(.disabled) {
        background: #2e7d32;
        color: #fff;
        border-color: #2e7d32;
    }

    .page-arrow.disabled {
        color: #ccc;
        cursor: not-allowed;
        background: #f5f5f5;
    }

    .page-info {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }
</style>
@endpush
