@if ($paginator->hasPages())
    <nav class="custom-pagination" role="navigation" aria-label="ページナビゲーション">
        {{-- 結果表示: ( 21 ~ 40 of 100 ) --}}
        <div class="pagination-info">
            ( {{ $paginator->firstItem() }} ~ {{ $paginator->lastItem() }} of {{ $paginator->total() }} )
        </div>

        {{-- ページネーションリンク --}}
        <div class="pagination-links">
            {{-- 前のページへのリンク --}}
            @if ($paginator->onFirstPage())
                {{-- 最初のページの場合は非表示 --}}
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-arrow" aria-label="前のページ">&lt;</a>
            @endif

            {{-- ページ番号 --}}
            @foreach ($elements as $element)
                {{-- "..." の省略記号 --}}
                @if (is_string($element))
                    <span class="pagination-dots">{{ $element }}</span>
                @endif

                {{-- ページ番号リンクの配列 --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-number active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- 次のページへのリンク --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-arrow" aria-label="次のページ">&gt;</a>
            @else
                {{-- 最後のページの場合は非表示 --}}
            @endif
        </div>
    </nav>
@endif