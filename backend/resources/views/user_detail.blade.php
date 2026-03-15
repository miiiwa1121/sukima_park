{{--
============================================================
ユーザ詳細画面 (user_detail.blade.php)
============================================================

【対応画面定義】
- admin_user_detail_screen.html（ユーザ詳細画面）

【このファイルの役割】
- 管理者向けユーザ詳細情報の表示
- ステータスのみ編集可能

【受け取るデータ】
- $user: ユーザー情報
→ UserController@show から渡される
→ Userモデル

【画面構成】
- アイコン画像（表示のみ）
- ログインID（表示のみ）
- ユーザ名、メールアドレス、電話番号、生年月日（表示のみ）
- 性別、公開設定（表示のみ）
- ステータス（編集可能）
- 自己紹介（表示のみ）
- 更新ボタン

============================================================
--}}

{{-- layouts/app.blade.phpを継承（共通のHTML構造を使用） --}}
@extends('layouts.admin')

{{-- ページタイトル（<title>タグの内容） --}}
    @section('title', 'ユーザー詳細 - スキマパーク管理')

    {{-- メインコンテンツ開始 --}}
    @section('content')
        <div class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">ユーザー詳細</h2>
                    <a href="{{ url('/admin/users') }}" class="view-all">← ユーザ一覧に戻る</a>
                </div>

                <div class="detail-box">
                    <form action="{{ url('/admin/users/' . $user->USER_ID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- アイコン画像（表示のみ） --}}
                        <div class="form-group">
                            <label>アイコン画像</label>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div
                                    style="width: 80px; height: 80px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 32px; overflow: hidden;">
                                    @if($user->avatar ?? null)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="アイコン"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        👤
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div>
                                {{-- ログインID（表示のみ） --}}
                                <div class="form-group">
                                    <label>ログインID</label>
                                    <div class="display-value">{{ $user->LOGIN_ID ?? $user->login_id ?? $user->id }}</div>
                                </div>

                                {{-- ユーザ名（表示のみ） --}}
                                <div class="form-group">
                                    <label>ユーザ名</label>
                                    <div class="display-value">{{ $user->NAME ?? $user->name ?? '未設定' }}</div>
                                </div>

                                {{-- メールアドレス（表示のみ） --}}
                                <div class="form-group">
                                    <label>メールアドレス</label>
                                    <div class="display-value">{{ $user->EMAIL ?? $user->email ?? '未設定' }}</div>
                                </div>

                                {{-- 電話番号（表示のみ） --}}
                                <div class="form-group">
                                    <label>電話番号</label>
                                    <div class="display-value">{{ $user->PHONE ?? $user->phone ?? '未設定' }}</div>
                                </div>

                                {{-- 生年月日（表示のみ） --}}
                                <div class="form-group">
                                    <label>生年月日</label>
                                    <div class="display-value">{{ $user->BIRTHDAY ?? $user->birthday ?? '未設定' }}</div>
                                </div>
                            </div>

                            <div>
                                {{-- 性別（表示のみ） --}}
                                <div class="form-group">
                                    <label>性別</label>
                                    <div class="display-value">
                                        @php
                                            $genderValue = $user->GENDER ?? $user->gender ?? '';
                                            $genderLabels = [
                                                'male' => '男性',
                                                'female' => '女性',
                                                'other' => 'その他',
                                                'not_specified' => '未設定',
                                            ];
                                        @endphp
                                        {{ $genderLabels[$genderValue] ?? '未設定' }}
                                    </div>
                                </div>

                                {{-- 生年月日の公開設定（表示のみ） --}}
                                <div class="form-group">
                                    <label>生年月日の公開設定</label>
                                    <div class="display-value">
                                        {{ ($user->BIRTHDAY_PUBLIC ?? $user->birthday_public ?? 'private') === 'public' ? '公開' : '非公開' }}
                                    </div>
                                </div>

                                {{-- 性別の公開設定（表示のみ） --}}
                                <div class="form-group">
                                    <label>性別の公開設定</label>
                                    <div class="display-value">
                                        {{ ($user->GENDER_PUBLIC ?? $user->gender_public ?? 'public') === 'public' ? '公開' : '非公開' }}
                                    </div>
                                </div>

                                {{-- ステータス（編集可能） --}}
                                {{-- ACCOUNT_STATUS: "0" = 有効, "1" = 利用停止中 --}}
                                <div class="form-group">
                                    <label for="status">ステータス</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="0" {{ ($user->ACCOUNT_STATUS ?? '0') == '0' ? 'selected' : '' }}>有効
                                        </option>
                                        <option value="1" {{ ($user->ACCOUNT_STATUS ?? '0') == '1' ? 'selected' : '' }}>利用停止中
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- 自己紹介（表示のみ） --}}
                        <div class="form-group">
                            <label>自己紹介</label>
                            <div class="display-value display-value-multiline">{{ $user->BIO ?? $user->bio ?? '未設定' }}</div>
                        </div>

                        <div class="detail-actions">
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">削除する</button>
                            <button type="submit" class="btn btn-primary">更新する</button>
                        </div>
                    </form>

                    {{-- 削除用フォーム（非表示） --}}
                    <form id="delete-form" action="{{ url('/admin/users/' . $user->USER_ID) }}" method="POST"
                        style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        <script>
            function confirmDelete() {
                if (confirm('本当にこのユーザーを削除しますか？この操作は取り消せません。')) {
                    document.getElementById('delete-form').submit();
                }
            }
        </script>
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

            .form-control:focus {
                outline: none;
                border-color: #2e7d32;
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

            .display-value {
                padding: 10px 12px;
                background: #f9f9f9;
                border: 1px solid #e0e0e0;
                border-radius: 6px;
                font-size: 14px;
                color: #333;
                min-height: 42px;
                display: flex;
                align-items: center;
            }

            .display-value-multiline {
                min-height: 100px;
                white-space: pre-wrap;
                align-items: flex-start;
                padding: 12px;
            }

            .detail-box {
                background: #fff;
                border-radius: 8px;
                padding: 32px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                max-width: 800px;
                margin: 0 auto;
            }

            .detail-actions {
                margin-top: 24px;
                padding-top: 24px;
                border-top: 1px solid #e0e0e0;
                display: flex;
                gap: 12px;
                justify-content: flex-end;
            }

            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
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

            .btn-danger {
                background: #d32f2f;
                color: #fff;
            }

            .btn-danger:hover {
                background: #c62828;
            }

            @media (max-width: 768px) {
                .form-grid {
                    grid-template-columns: 1fr;
                }

                .section-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 12px;
                }

                .detail-actions {
                    flex-direction: column-reverse;
                }

                .detail-actions .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush