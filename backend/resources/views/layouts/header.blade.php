<header class="header">
    <div class="header-container">
        {{-- 左側：ロゴ（トップページへ） --}}
        <a href="{{ url('/') }}" class="logo">
            <span class="logo-text">スキマパーク</span>
        </a>
        
        {{-- 右側：ナビゲーション --}}
        <nav class="nav">
            @auth
                {{-- ログイン済み --}}
                <ul class="nav-list">
                    <li>
                    <a href="{{ route('land.register') }}" class="btn btn-primary">
                            土地を登録
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('messages.index') }}" class="nav-icon-btn" title="メッセージ">
                            💬
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mypage') }}" class="nav-icon-btn user-icon" title="マイページ">
                            @if(Auth::user()->ICON_IMAGE && Auth::user()->ICON_IMAGE !== 'default_icon.png')
                                <img src="{{ asset('storage/' . Auth::user()->ICON_IMAGE) }}" alt="アイコン">
                            @else
                                <span class="default-icon">👤</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-btn nav-icon-btn"title="ログアウト">🚪</button>
                        </form>
                    </li>
                </ul>
            @else
                {{-- 未ログイン --}}
                <ul class="nav-list">
                    <li>
                        <a href="{{ url('/login') }}" class="btn btn-primary">
                            ログイン
                        </a>
                    </li>
                </ul>
            @endauth
        </nav>
    </div>
</header>
