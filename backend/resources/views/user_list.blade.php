{{--
============================================================
ユーザ一覧画面 (user_list.blade.php)
============================================================

【対応画面定義】
- admin_user_list_screen.html（ユーザ一覧画面）

【このファイルの役割】
- 管理者向けユーザ一覧を表示
- ユーザ検索・絞り込み機能
- ユーザ詳細・編集画面への遷移

【受け取るデータ】
- $users: ユーザーのコレクション
→ UserController@index から渡される
→ Userモデルのコレクション

【画面構成】
1. 絞り込みフォーム
- 名前 or Eメール（テキスト入力）
- ステータス（プルダウン）

2. ユーザ一覧
- アバター
- ユーザ名
- メールアドレス
- 登録日
- アクションボタン（詳細）

============================================================
--}}

{{-- layouts/app.blade.phpを継承（共通のHTML構造を使用） --}}
@extends('layouts.admin')

{{-- ページタイトル（<title>タグの内容） --}}
  @section('title', 'ユーザ一覧 - スキマパーク管理')

  {{-- メインコンテンツ開始 --}}
  @section('content')
    {{--
    =====================================================
    ユーザ一覧セクション
    =====================================================
    --}}
    <div class="section">
      <div class="container">
        <h2 class="section-title">ユーザ一覧</h2>

        {{--
        絞り込みフォーム
        GETリクエストで検索パラメータを送信
        --}}
        <div class="filter-box">
          <form action="{{ url('/admin/users') }}" method="GET" class="filter-form">
            {{-- 名前 or Eメール --}}
            <div class="filter-group" style="grid-column: 1 / span 2">
              <label for="keyword">名前 or Eメール</label>
              <input type="text" id="keyword" name="keyword" class="form-control" placeholder="例: 田中, taro@example.com"
                value="{{ request('keyword') }}">
            </div>

            {{-- ステータス --}}
            <div class="filter-group">
              <label for="status">ステータス</label>
              <select id="status" name="status" class="form-control">
                <option value="">すべて</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>有効</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>利用停止中</option>
              </select>
            </div>

            <button type="submit" class="filter-btn">絞り込む</button>
          </form>
        </div>

        {{--
        ユーザ一覧
        --}}
        <div class="card-grid">
          @forelse($users as $user)
            <div class="card">
              {{-- ユーザアバター --}}
              <div class="user-avatar">👤</div>

              {{-- ユーザ情報 --}}
              <div class="card-body">
                <h3 class="card-title">{{ $user->NAME ?? $user->name ?? '名前未設定' }}</h3>
                <p class="card-text">{{ $user->EMAIL ?? $user->email ?? '' }}</p>
                <p class="card-text card-date">
                  登録日: {{ $user->created_at?->format('Y-m-d') ?? '不明' }}
                </p>
              </div>

              {{-- アクションボタン --}}
              <div class="card-actions">
                <a href="{{ url('/admin/users/' . $user->USER_ID) }}" class="btn btn-secondary">詳細</a>
              </div>
            </div>
          @empty
            <div class="no-data">
              <p>ユーザが見つかりませんでした。</p>
            </div>
          @endforelse
        </div>

        {{-- ページネーション（ある場合） --}}
        @if(method_exists($users, 'links'))
          <div class="pagination-wrapper">
            {{ $users->appends(request()->query())->links() }}
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

      .section-title {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 24px;
        color: #222;
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
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
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
        transition: background 0.2s;
      }

      .filter-btn:hover {
        background: #1b5e20;
      }

      .card-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
      }

      .card {
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
        transition: box-shadow 0.2s;
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 10px 12px;
        gap: 10px;
      }

      .card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
      }

      .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
      }

      .card-body {
        flex: 1;
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        min-width: 0;
      }

      .card-title {
        font-size: 14px;
        font-weight: 600;
        color: #222;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 80px;
      }

      .card-text {
        font-size: 12px;
        color: #666;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
      }

      .card-date {
        font-size: 11px;
        color: #888;
        white-space: nowrap;
      }

      .card-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
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

      .no-data {
        text-align: center;
        color: #666;
        padding: 40px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
      }

      .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
      }

      /* レスポンシブ対応 */
      @media (max-width: 768px) {
        .filter-form {
          grid-template-columns: 1fr;
        }

        .filter-group[style*="grid-column"] {
          grid-column: 1 !important;
        }

        .card-grid {
          grid-template-columns: 1fr;
        }

        .card-body {
          flex-direction: column;
          align-items: flex-start;
          gap: 4px;
        }

        .card-title,
        .card-text {
          min-width: unset;
          white-space: normal;
        }
      }
    </style>
  @endpush