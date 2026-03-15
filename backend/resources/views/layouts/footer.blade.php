<footer class="footer">
    <div class="footer-container">
        {{-- ロゴとリンク --}}
        <div class="footer-main">
            <div class="footer-brand">
                <span class="footer-logo">スキマパーク</span>
                <p class="footer-tagline">あなたのスキマ、誰かの価値に。</p>
            </div>
            <div class="footer-links">
                <a href="{{ route('contact') }}" class="footer-link">📩 お問い合わせ</a>
            </div>
        </div>
        
        {{-- コピーライト --}}
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} スキマパーク All Rights Reserved.</p>
        </div>
    </div>
</footer>
