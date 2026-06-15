@php
    $paginator = $paginator ?? null;
@endphp

@if($paginator && ($paginator->hasPages() || $paginator->total() > 0))
    <div class="store-blog-pagination">
        @if($paginator->total() > 0)
            <p class="store-blog-pagination__info">
                عرض
                <span>{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span>
                من
                <strong>{{ $paginator->total() }}</strong>
                مقال
            </p>
        @endif

        @if($paginator->hasPages())
            <ul class="pagination store-blog-pagination__nav">
                <li class="prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if($paginator->onFirstPage())
                        <a href="#" aria-label="السابق" tabindex="-1" aria-disabled="true">
                            <i class="w-icon-long-arrow-right" aria-hidden="true"></i>
                            <span>السابق</span>
                        </a>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" aria-label="السابق">
                            <i class="w-icon-long-arrow-right" aria-hidden="true"></i>
                            <span>السابق</span>
                        </a>
                    @endif
                </li>

                @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                    <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}" aria-label="صفحة {{ $page }}" @if($page == $paginator->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                    </li>
                @endforeach

                <li class="next {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" aria-label="التالي">
                            <span>التالي</span>
                            <i class="w-icon-long-arrow-left" aria-hidden="true"></i>
                        </a>
                    @else
                        <a href="#" aria-label="التالي" tabindex="-1" aria-disabled="true">
                            <span>التالي</span>
                            <i class="w-icon-long-arrow-left" aria-hidden="true"></i>
                        </a>
                    @endif
                </li>
            </ul>
        @endif
    </div>
@endif
