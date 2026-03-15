@extends('layouts.app')

@section('title', 'メッセージ')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
          "Hiragino Sans", sans-serif;
        line-height: 1.6;
        color: #333;
        background: #fafafa;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    header {
        background: #fff;
        border-bottom: 1px solid #e0e0e0;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
    }

    .logo {
        font-size: 18px;
        font-weight: 600;
        color: #2e7d32;
        text-decoration: none;
    }

    .header-nav {
        display: flex;
        gap: 12px;
        align-items: center;
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

    .icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f5f5f5;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
    }

    .icon-btn:hover {
        background: #e0e0e0;
    }

    .main-content {
        padding: 40px 0;
    }

    .dm-header {
        padding: 25px 30px;
        border-bottom: 2px solid #e8f5e9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dm-header h2 {
        color: #2e7d32;
        font-size: 24px;
    }

    .new-message-btn {
        background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 10px rgba(76, 175, 80, 0.3);
    }

    .new-message-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
    }

    .search-box {
        padding: 15px 30px;
        border-bottom: 1px solid #e8f5e9;
    }

    .search-input {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid #c8e6c9;
        border-radius: 25px;
        font-size: 14px;
        background: #f1f8f4;
        transition: all 0.3s;
    }

    .search-input:focus {
        outline: none;
        border-color: #4caf50;
        background: white;
    }

    .search-input::placeholder {
        color: #a5d6a7;
    }

    .dm-list {
        flex: 1;
        overflow-y: auto;
    }

    .dm-item {
        display: flex;
        padding: 20px 30px;
        border-bottom: 1px solid #f1f8f4;
        cursor: pointer;
        transition: background 0.2s;
        position: relative;
    }

    .dm-item:hover {
        background: #f1f8f4;
    }

    .dm-item.unread {
        background: #e8f5e9;
    }

    .dm-item.unread:hover {
        background: #c8e6c9;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #66bb6a, #4caf50);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        font-weight: bold;
        flex-shrink: 0;
        margin-right: 15px;
    }

    .dm-content {
        flex: 1;
        min-width: 0;
    }

    .dm-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .dm-name {
        color: #2e7d32;
        font-weight: 600;
        font-size: 16px;
    }

    .dm-time {
        color: #81c784;
        font-size: 12px;
        white-space: nowrap;
        margin-left: 10px;
    }

    .dm-preview {
        color: #666;
        font-size: 14px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dm-item.unread .dm-preview {
        color: #333;
        font-weight: 500;
    }

    .unread-badge {
        position: absolute;
        top: 20px;
        right: 30px;
        background: #4caf50;
        color: white;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 12px;
        font-weight: 600;
    }

    .no-messages {
        text-align: center;
        padding: 60px 30px;
        color: #81c784;
    }

    .no-messages-icon {
        font-size: 64px;
        margin-bottom: 15px;
    }

    .no-messages-text {
        font-size: 18px;
        margin-bottom: 20px;
        color: #66bb6a;
    }

    .dm-list::-webkit-scrollbar {
        width: 8px;
    }

    .dm-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .dm-list::-webkit-scrollbar-thumb {
        background: #c8e6c9;
        border-radius: 4px;
    }

    .dm-list::-webkit-scrollbar-thumb:hover {
        background: #81c784;
    }
</style>
@endpush

@section('content')
<div class="section">
    <div class="container">
        <div class="dm-header">
            <h2>メッセージ</h2>
            <button class="new-message-btn" onclick="createNewMessage()">✉️ 新規メッセージ</button>
        </div>

        <div class="search-box">
            <input type="text" class="search-input" placeholder="🔍 メッセージを検索" id="searchInput">
        </div>

        <div class="dm-list" id="dmList">
            @forelse($messages ?? [] as $message)
                <div class="dm-item {{ $message->unread ? 'unread' : '' }}">
                    <a href="{{ route('user.show', $message->id) }}" class="avatar" onclick="event.stopPropagation();" style="text-decoration: none; color: inherit;">
                        {{ mb_substr($message->sender_name, 0, 1) }}
                    </a>
                    <div class="dm-content" onclick="openDM({{ $message->id }})" style="cursor: pointer; flex: 1;">
                        <div class="dm-top">
                            <span class="dm-name">{{ $message->sender_name }}</span>
                            <span class="dm-time">{{ $message->time_ago }}</span>
                        </div>
                        <div class="dm-preview">{{ $message->preview }}</div>
                    </div>
                    @if($message->unread)
                        <div class="unread-badge">{{ $message->unread_count }}</div>
                    @endif
                </div>
            @empty
                <div class="no-messages">
                    <div class="no-messages-icon">💬</div>
                    <div class="no-messages-text">メッセージがありません</div>
                    <button class="new-message-btn" onclick="createNewMessage()">新規メッセージを作成</button>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // サンプルデータ（開発用）
    const dmData = [
        {
            id: 1,
            name: '田中 花子',
            avatar: '田',
            preview: 'ありがとうございます！また連絡させていただきます。',
            time: '5分前',
            unread: true,
            unreadCount: 2
        },
        {
            id: 2,
            name: '佐藤 健',
            avatar: '佐',
            preview: '了解しました。よろしくお願いします。',
            time: '1時間前',
            unread: false
        },
        {
            id: 3,
            name: '鈴木 美咲',
            avatar: '鈴',
            preview: '明日の件ですが、10時からでも大丈夫でしょうか？',
            time: '3時間前',
            unread: true,
            unreadCount: 1
        },
        {
            id: 4,
            name: '高橋 太郎',
            avatar: '高',
            preview: 'お疲れ様です。資料の件、確認しました。',
            time: '昨日',
            unread: false
        },
        {
            id: 5,
            name: '山本 咲',
            avatar: '山',
            preview: 'それでは次回お会いした時に詳しくお話しましょう。',
            time: '昨日',
            unread: false
        },
        {
            id: 6,
            name: '中村 誠',
            avatar: '中',
            preview: 'ご連絡ありがとうございます。',
            time: '2日前',
            unread: false
        },
        {
            id: 7,
            name: '小林 愛',
            avatar: '小',
            preview: '写真送っていただけますか？',
            time: '3日前',
            unread: false
        },
        {
            id: 8,
            name: '加藤 翔',
            avatar: '加',
            preview: 'わかりました！ではまた後ほど。',
            time: '4日前',
            unread: false
        }
    ];

    // 開発環境でサンプルデータを使用
    @if(empty($messages))
    document.addEventListener('DOMContentLoaded', function() {
        renderDMList(dmData);
    });
    @endif

    // DM一覧を表示
    function renderDMList(data) {
        const dmList = document.getElementById('dmList');
        
        if (data.length === 0) {
            dmList.innerHTML = `
                <div class="no-messages">
                    <div class="no-messages-icon">💬</div>
                    <div class="no-messages-text">メッセージがありません</div>
                    <button class="new-message-btn" onclick="createNewMessage()">新規メッセージを作成</button>
                </div>
            `;
            return;
        }

        dmList.innerHTML = data.map(dm => `
            <div class="dm-item ${dm.unread ? 'unread' : ''}" onclick="openDM(${dm.id})">
                <div class="avatar">${dm.avatar}</div>
                <div class="dm-content">
                    <div class="dm-top">
                        <span class="dm-name">${dm.name}</span>
                        <span class="dm-time">${dm.time}</span>
                    </div>
                    <div class="dm-preview">${dm.preview}</div>
                </div>
                ${dm.unread ? `<div class="unread-badge">${dm.unreadCount}</div>` : ''}
            </div>
        `).join('');
    }

    // 検索機能
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        @if(!empty($messages))
        // サーバーサイドの検索を実装する場合
        // window.location.href = `{{ route('messages.index') }}?search=${searchTerm}`;
        @else
        // クライアントサイドの検索（開発用）
        const filteredData = dmData.filter(dm => 
            dm.name.toLowerCase().includes(searchTerm) || 
            dm.preview.toLowerCase().includes(searchTerm)
        );
        renderDMList(filteredData);
        @endif
    });

    // DMを開く
    function openDM(id) {
        window.location.href = `{{ url('messages') }}/${id}`;
    }

    // 新規メッセージ作成
    function createNewMessage() {
        window.location.href = `{{ route('messages.create') }}`;
    }
</script>
@endpush