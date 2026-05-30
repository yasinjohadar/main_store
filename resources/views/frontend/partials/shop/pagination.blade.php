@if($products->hasPages() || $products->total() > 0)
    <div class="toolbox toolbox-pagination justify-content-between store-shop-pagination">
        <p class="showing-info mb-2 mb-sm-0 store-shop-pagination__info">
            عرض
            <span>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span>
            من
            <strong>{{ $products->total() }}</strong>
            منتج
        </p>
        @if($products->hasPages())
            <ul class="pagination store-shop-pagination__nav">
                <li class="prev {{ $products->onFirstPage() ? 'disabled' : '' }}">
                    @if($products->onFirstPage())
                        <a href="#" aria-label="الصفحة السابقة" tabindex="-1" aria-disabled="true">
                            <i class="w-icon-long-arrow-right"></i>
                            <span>السابق</span>
                        </a>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" aria-label="الصفحة السابقة">
                            <i class="w-icon-long-arrow-right"></i>
                            <span>السابق</span>
                        </a>
                    @endif
                </li>

                @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                    <li class="page-item {{ $page == $products->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}" aria-label="صفحة {{ $page }}">{{ $page }}</a>
                    </li>
                @endforeach

                <li class="next {{ $products->hasMorePages() ? '' : 'disabled' }}">
                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" aria-label="الصفحة التالية">
                            <span>التالي</span>
                            <i class="w-icon-long-arrow-left"></i>
                        </a>
                    @else
                        <a href="#" aria-label="الصفحة التالية" tabindex="-1" aria-disabled="true">
                            <span>التالي</span>
                            <i class="w-icon-long-arrow-left"></i>
                        </a>
                    @endif
                </li>
            </ul>
        @endif
    </div>
@endif
