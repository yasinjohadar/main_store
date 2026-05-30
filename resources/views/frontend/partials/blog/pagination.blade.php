@php
    $paginator = $paginator ?? null;
@endphp

@if($paginator && $paginator->hasPages())
    <ul class="pagination justify-content-center mb-10 pb-2 pt-2 store-blog-pagination">
        <li class="prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            @if($paginator->onFirstPage())
                <a href="#" aria-label="السابق" tabindex="-1" aria-disabled="true">
                    <i class="w-icon-long-arrow-right"></i>السابق
                </a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" aria-label="السابق">
                    <i class="w-icon-long-arrow-right"></i>السابق
                </a>
            @endif
        </li>

        @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
        @endforeach

        <li class="next {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" aria-label="التالي">
                    التالي<i class="w-icon-long-arrow-left"></i>
                </a>
            @else
                <a href="#" aria-label="التالي" tabindex="-1" aria-disabled="true">
                    التالي<i class="w-icon-long-arrow-left"></i>
                </a>
            @endif
        </li>
    </ul>
@endif
