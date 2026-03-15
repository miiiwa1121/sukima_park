{{--
============================================================
問い合わせ詳細画面 (contact_detail.blade.php)
============================================================

【対応画面定義】
- 管理画面 - 問い合わせ詳細

【このファイルの役割】
- 管理者向け問い合わせ詳細の表示
- ステータスの変更
- 返信の送信

【受け取るデータ】
- $contact: 問い合わせデータ（senderリレーション含む）
- CONTACT_ID: 問い合わせID
- TITLE: 件名
- MESSAGE: 問い合わせ内容
- DATE: 問い合わせ日
- STATUS: ステータス (0=新規, 1=対応中, 2=完了)
- sender: 送信者（MEMBER_TABLE）

【画面構成】
1. ヘッダー（管理者用共通ヘッダー）
2. 問い合わせ詳細ボックス
3. ステータス変更フォーム
4. 返信フォーム

============================================================
--}}

{{-- 管理者用レイアウトを継承 --}}
@extends('layouts.admin')

{{-- ページタイトル --}}
@section('title', '問い合わせ詳細')

{{-- メインコンテンツ --}}
@section('content')
    <div class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">問い合わせ詳細 (ID: {{ $contact->CONTACT_ID ?? '---' }})</h2>
                <a href="{{ url('/admin/contact_list') }}" class="view-all">← 問い合わせ一覧に戻る</a>
            </div>

            {{-- 成功メッセージ --}}
            @if(session('success'))
                <div class="alert alert-success" style="max-width: 800px; margin: 0 auto 20px auto;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- エラーメッセージ --}}
            @if(session('error'))
                <div class="alert alert-error" style="max-width: 800px; margin: 0 auto 20px auto;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="detail-box">
                <div class="detail-item">
                    <label>日付</label>
                    <div class="detail-value">{{ $contact->DATE?->format('Y-m-d') ?? $contact->DATE ?? '---' }}</div>
                </div>
                <div class="detail-item detail-item-inline">
                    <label>ユーザー名</label>
                    <div class="detail-value">
                        @if($contact->sender ?? null)
                            <a
                                href="{{ url('/admin/users/' . $contact->sender->USER_ID) }}">{{ $contact->sender->NAME ?? $contact->sender->USERNAME ?? '不明' }}</a>
                            <span class="user-email">({{ $contact->sender->EMAIL ?? '---' }})</span>
                        @else
                            不明
                        @endif
                    </div>
                </div>
                <div class="detail-item">
                    <label>件名</label>
                    <div class="detail-value">{{ $contact->TITLE ?? '---' }}</div>
                </div>
                <div class="detail-item">
                    <label>問い合わせ内容</label>
                    <div class="detail-value">{{ $contact->MESSAGE ?? '---' }}</div>
                </div>

                <form class="status-form" method="POST"
                    action="{{ url('/admin/contact/' . ($contact->CONTACT_ID ?? 0) . '/status') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="status">ステータスを変更</label>
                        <div class="status-select-wrapper">
                            <select id="status" name="status" class="form-control status-select">
                                <option value="0" {{ ($contact->STATUS ?? 0) == 0 ? 'selected' : '' }}>新規</option>
                                <option value="1" {{ ($contact->STATUS ?? 0) == 1 ? 'selected' : '' }}>対応中</option>
                                <option value="2" {{ ($contact->STATUS ?? 0) == 2 ? 'selected' : '' }}>完了</option>
                            </select>
                            <button type="submit" class="btn btn-secondary">変更</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="reply-box">
                <h3>返信する</h3>
                <form method="POST" action="{{ url('/admin/contact/' . ($contact->CONTACT_ID ?? 0) . '/reply') }}">
                    @csrf
                    <div class="form-group">
                        <label for="reply_body">返信内容</label>
                        <textarea id="reply_body" name="reply_body" class="form-control" placeholder="{{ ($contact->sender->NAME ?? $contact->sender->USERNAME ?? 'お客') }}様

                お問い合わせありがとうございます。
                スキマパークサポート担当です。"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">返信を送信</button>
                </form>
            </div>
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

        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
            color: #555;
        }

        /* 詳細表示用のスタイル */
        .detail-box {
            background: #fff;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto 24px auto;
        }

        .detail-item {
            margin-bottom: 16px;
        }

        .detail-item label {
            font-size: 14px;
            font-weight: 500;
            color: #555;
            margin-bottom: 4px;
            display: block;
        }

        .detail-value {
            font-size: 15px;
            color: #222;
            padding: 10px 12px;
            background: #f9f9f9;
            border-radius: 6px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .detail-value a {
            color: #2e7d32;
            text-decoration: none;
        }

        .detail-value a:hover {
            text-decoration: underline;
        }

        /* ユーザー名の横一列表示 */
        .detail-item-inline .detail-value {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .user-email {
            color: #666;
            font-size: 14px;
        }

        /* ステータス変更フォーム */
        .status-form {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
        }

        .status-select-wrapper {
            display: flex;
            gap: 12px;
            justify-content: flex-start;
        }

        .status-select {
            width: 150px;
            flex-shrink: 0;
        }

        .reply-box {
            background: #fff;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }

        .reply-box h3 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .reply-box textarea {
            min-height: 150px;
            resize: vertical;
        }

        .reply-box .btn {
            width: 100%;
            padding-top: 12px;
            padding-bottom: 12px;
            font-size: 16px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
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
    </style>
@endpush