@extends('layouts.app')

@section('title', '新規メッセージ')

@push('styles')
<style>
    .main-content {
        padding: 40px 0;
    }

    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .page-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .back-btn {
        background: none;
        border: none;
        color: #4caf50;
        font-size: 24px;
        cursor: pointer;
        padding: 5px;
        text-decoration: none;
    }

    .page-title {
        color: #2e7d32;
        font-size: 24px;
        font-weight: 600;
    }

    .search-box {
        margin-bottom: 20px;
    }

    .search-input {
        width: 100%;
        padding: 14px 20px;
        font-size: 16px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        outline: none;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: #4caf50;
    }

    .search-input::placeholder {
        color: #aaa;
    }

    .user-list {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        min-height: 100px;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .user-item:hover {
        background: #f5f5f5;
    }

    .user-item:last-child {
        border-bottom: none;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #66bb6a, #4caf50);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
        margin-right: 15px;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .user-email {
        font-size: 13px;
        color: #888;
        margin-top: 2px;
    }

    .no-users {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }

    .search-hint {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }

    .loading {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }
</style>
@endpush

@section('content')
<main class="main-content">
    <div class="container">
        <div class="page-header">
            <a href="{{ route('messages.index') }}" class="back-btn">←</a>
            <h1 class="page-title">新規メッセージ</h1>
        </div>

        <div class="search-box">
            <input 
                type="text" 
                class="search-input" 
                id="userSearch" 
                placeholder="ユーザー名を入力して検索..."
                autocomplete="off"
            >
        </div>

        <div class="user-list" id="userList">
            <div class="search-hint">
                ユーザー名を入力して検索してください
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('userSearch');
    const userList = document.getElementById('userList');
    const searchUrl = '{{ route("messages.search") }}';
    const csrfToken = '{{ csrf_token() }}';
    let searchTimeout = null;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // 前回のタイマーをクリア
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        if (query.length < 1) {
            userList.innerHTML = '<div class="search-hint">ユーザー名を入力して検索してください</div>';
            return;
        }

        // 300ms後に検索実行（タイピング中の連続リクエストを防ぐ）
        searchTimeout = setTimeout(() => {
            searchUsers(query);
        }, 300);
    });

    async function searchUsers(query) {
        userList.innerHTML = '<div class="loading">検索中...</div>';

        try {
            const response = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                const users = await response.json();
                displayUsers(users);
            } else {
                userList.innerHTML = '<div class="no-users">検索に失敗しました</div>';
            }
        } catch (error) {
            console.error('Search error:', error);
            userList.innerHTML = '<div class="no-users">検索に失敗しました</div>';
        }
    }

    function displayUsers(users) {
        if (users.length === 0) {
            userList.innerHTML = '<div class="no-users">該当するユーザーが見つかりません</div>';
            return;
        }

        let html = '';
        users.forEach(user => {
            html += `
                <a href="/messages/${user.id}" class="user-item">
                    <div class="user-avatar">${user.initial}</div>
                    <div class="user-info">
                        <div class="user-name">${escapeHtml(user.name)}</div>
                        <div class="user-email">${escapeHtml(user.email)}</div>
                    </div>
                </a>
            `;
        });
        userList.innerHTML = html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ページ読み込み時に検索欄にフォーカス
    searchInput.focus();
</script>
@endpush
