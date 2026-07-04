{{-- CSSフレームワークを使わない自前のページネーション。
     Laravel標準のビューはTailwind/Bootstrap前提なので、
     paginatorのメソッド(onFirstPage/hasMorePages/xxxPageUrl)で組み立てる --}}
@if ($paginator->hasPages())
    <nav class="pagination">
        @if ($paginator->onFirstPage())
            <span class="disabled">← 前へ</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">← 前へ</a>
        @endif

        <span class="current">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} ページ</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">次へ →</a>
        @else
            <span class="disabled">次へ →</span>
        @endif
    </nav>
@endif
