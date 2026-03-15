@extends('layouts.app')

@section('title', 'メッセージ - ' . $recipient->name)

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

    .chat-header {
        padding: 20px 30px;
        border-bottom: 2px solid #e8f5e9;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .back-btn {
        background: none;
        border: none;
        color: #4caf50;
        font-size: 24px;
        cursor: pointer;
        padding: 5px;
        transition: transform 0.2s;
    }

    .back-btn:hover {
        transform: translateX(-3px);
    }

    .chat-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .chat-avatar {
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
    }

    .chat-user-name {
        color: #2e7d32;
        font-size: 18px;
        font-weight: 600;
    }

    .chat-menu-btn {
        background: none;
        border: none;
        color: #4caf50;
        font-size: 24px;
        cursor: pointer;
        padding: 5px;
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 30px;
        padding-bottom: 120px;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .message {
        display: flex;
        gap: 10px;
        max-width: 70%;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message.received {
        align-self: flex-start;
    }

    .message.sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #66bb6a, #4caf50);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        font-weight: bold;
        flex-shrink: 0;
    }

    .message-content {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        word-wrap: break-word;
        line-height: 1.5;
    }

    .message.received .message-bubble {
        background: white;
        color: #333;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
    }

    .message.sent .message-bubble {
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-time {
        font-size: 11px;
        color: #999;
        padding: 0 8px;
    }

    .message.sent .message-time {
        text-align: right;
    }

    .date-separator {
        text-align: center;
        color: #999;
        font-size: 12px;
        padding: 10px 0;
        position: relative;
    }

    .date-separator::before,
    .date-separator::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #e0e0e0;
    }

    .date-separator::before {
        left: 0;
    }

    .date-separator::after {
        right: 0;
    }

    .input-area {
        padding: 20px 30px;
        border-top: 2px solid #e8f5e9;
        background: white;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 50;
    }

    .input-container {
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .attachment-btn {
        background: #f1f8f4;
        border: none;
        color: #4caf50;
        font-size: 24px;
        cursor: pointer;
        padding: 10px;
        border-radius: 50%;
        transition: all 0.2s;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .attachment-btn:hover {
        background: #c8e6c9;
    }

    .input-wrapper {
        flex: 1;
        position: relative;
    }

    .message-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #c8e6c9;
        border-radius: 25px;
        font-size: 15px;
        resize: none;
        max-height: 216px;
        min-height: 45px;
        font-family: inherit;
        transition: all 0.3s;
    }

    .message-input:focus {
        outline: none;
        border-color: #4caf50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .message-input::placeholder {
        color: #a5d6a7;
    }

    .send-btn {
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 10px;
        border-radius: 50%;
        transition: all 0.3s;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(76, 175, 80, 0.3);
    }

    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
    }

    .send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .messages-container::-webkit-scrollbar {
        width: 8px;
    }

    .messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .messages-container::-webkit-scrollbar-thumb {
        background: #c8e6c9;
        border-radius: 4px;
    }

    .messages-container::-webkit-scrollbar-thumb:hover {
        background: #81c784;
    }
</style>
@endpush

@section('content')
<div class="section">
    <div class="container">
        <div class="chat-header">
            <a href="{{ route('messages.index') }}" class="back-btn">←</a>
            <a href="{{ route('mypage', $recipient->id) }}" class="chat-user-info" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                <div class="chat-avatar">{{ mb_substr($recipient->name ?? '相手', 0, 1) }}</div>
                <div class="chat-user-name">{{ $recipient->name ?? '相手' }}</div>
            </a>
            <button class="chat-menu-btn">⋮</button>
        </div>

        <div class="messages-container" id="messagesContainer">
            @php
                $currentDate = null;
            @endphp

            @foreach($messages ?? [] as $message)
                @php
                    $messageDate = \Carbon\Carbon::parse($message->created_at)->format('Y年m月d日');
                @endphp

                @if($currentDate !== $messageDate)
                    <div class="date-separator">{{ $messageDate }}</div>
                    @php
                        $currentDate = $messageDate;
                    @endphp
                @endif

                <div class="message @if($message->is_sent) sent @else received @endif">
                    <div class="message-avatar">
                        @if($message->is_sent)
                            私
                        @else
                            <a href="{{ route('mypage', $recipient->id) }}" style="text-decoration: none; color: inherit;">
                                {{ mb_substr($recipient->name ?? '相手', 0, 1) }}
                            </a>
                        @endif
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">{{ $message->content }}</div>
                        <div class="message-time">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="input-area">
            <div class="input-container">
                <button class="attachment-btn" onclick="attachFile()">📎</button>
                <div class="input-wrapper">
                    <textarea 
                        class="message-input" 
                        id="messageInput" 
                        placeholder="メッセージを入力..."
                        rows="1"
                    ></textarea>
                </div>
                <button class="send-btn" id="sendBtn" onclick="sendMessage()">➤</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const csrfToken = '{{ csrf_token() }}';
    const messageStoreUrl = '{{ route("messages.store") }}';

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    scrollToBottom();

    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 216) + 'px';
        sendBtn.disabled = this.value.trim() === '';
    });

    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const text = messageInput.value.trim();
        if (text === '') return;

        const now = new Date();
        const time = now.getHours().toString().padStart(2, '0') + ':' + 
                    now.getMinutes().toString().padStart(2, '0');

        const messageHTML = 
            '<div class="message sent">' +
                '<div class="message-avatar">私</div>' +
                '<div class="message-content">' +
                    '<div class="message-bubble">' + escapeHtml(text) + '</div>' +
                    '<div class="message-time">' + time + '</div>' +
                '</div>' +
            '</div>';

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        messageInput.value = '';
        messageInput.style.height = 'auto';
        sendBtn.disabled = true;
        scrollToBottom();

        fetch(messageStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                recipient_id: {{ $recipient->id ?? 0 }},
                content: text
            })
        }).catch(error => {
            console.error('Error:', error);
        });
    }

    function attachFile() {
        alert('ファイル添付機能（実装予定）');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ===== ポーリングによるリアルタイム更新 =====
    const pollUrl = '{{ route("messages.poll", $recipient->id) }}';
    const recipientId = {{ $recipient->id }};
    const recipientName = '{{ $recipient->name ?? "相手" }}';
    const recipientInitial = '{{ mb_substr($recipient->name ?? "相手", 0, 1) }}';
    let lastMessageId = {{ $messages->isNotEmpty() ? $messages->last()->id ?? 0 : 0 }};
    let isPolling = true;

    // 新着メッセージをチェック
    async function pollMessages() {
        if (!isPolling) return;

        try {
            const response = await fetch(`${pollUrl}?last_id=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        // 送信済みでない新着メッセージのみ追加
                        if (!msg.is_sent) {
                            appendMessage(msg);
                        }
                    });
                    lastMessageId = data.last_id;
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }

        // 3秒後に再度チェック
        setTimeout(pollMessages, 3000);
    }

    // メッセージをDOMに追加
    function appendMessage(msg) {
        let avatarContent;
        if (msg.is_sent) {
            avatarContent = '私';
        } else {
            avatarContent = `<a href="/mypage/${recipientId}" style="text-decoration: none; color: inherit;">${recipientInitial}</a>`;
        }

        const messageHTML = 
            '<div class="message ' + (msg.is_sent ? 'sent' : 'received') + '">' +
                '<div class="message-avatar">' + avatarContent + '</div>' +
                '<div class="message-content">' +
                    '<div class="message-bubble">' + escapeHtml(msg.content) + '</div>' +
                    '<div class="message-time">' + msg.time + '</div>' +
                '</div>' +
            '</div>';

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        scrollToBottom();
    }

    // ページ離脱時にポーリング停止
    window.addEventListener('beforeunload', () => {
        isPolling = false;
    });

    // ポーリング開始
    setTimeout(pollMessages, 3000);
</script>
@endpush