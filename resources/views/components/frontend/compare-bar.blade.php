@php
    $compareProducts = $compareProducts ?? collect();
    $compareCount = $compareProducts->count();
    $emptySlots = max(0, \App\Services\CompareService::MAX_ITEMS - $compareCount);
@endphp

<div class="store-compare-bar{{ $compareCount > 0 ? ' is-visible' : '' }}" data-compare-bar aria-hidden="{{ $compareCount > 0 ? 'false' : 'true' }}">
    <div class="container store-compare-bar__inner">
        <div class="store-compare-bar__head">
            <span class="store-compare-bar__icon"><i class="w-icon-compare"></i></span>
            <div>
                <h2 class="store-compare-bar__title">مقارنة المنتجات</h2>
                <p class="store-compare-bar__count" data-compare-bar-count>
                    (<span>{{ $compareCount }}</span> {{ $compareCount === 1 ? 'منتج' : 'منتجات' }})
                </p>
            </div>
        </div>

        <ul class="store-compare-bar__list" data-compare-bar-list>
            @foreach($compareProducts as $product)
                <li class="store-compare-bar__item" data-compare-item="{{ $product->id }}">
                    <a href="{{ route('frontend.product.show', $product->slug) }}" class="store-compare-bar__thumb">
                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" width="72" height="72" loading="lazy">
                    </a>
                    <button type="button" class="store-compare-bar__remove btn-compare-bar-remove"
                        data-delete-url="{{ route('frontend.compare.destroy', $product) }}"
                        aria-label="إزالة {{ $product->name }}">
                        <i class="fas fa-times"></i>
                    </button>
                </li>
            @endforeach
            @for($i = 0; $i < $emptySlots; $i++)
                <li class="store-compare-bar__item store-compare-bar__item--empty" aria-hidden="true">
                    <span class="store-compare-bar__placeholder"><i class="w-icon-plus"></i></span>
                </li>
            @endfor
        </ul>

        <div class="store-compare-bar__actions">
            <button type="button" class="store-compare-bar__clear btn-compare-bar-clear">مسح الكل</button>
            <a href="{{ route('frontend.compare') }}"
                class="store-compare-bar__start{{ $compareCount < 2 ? ' is-disabled' : '' }}"
                data-compare-bar-start
                @if($compareCount < 2) aria-disabled="true" tabindex="-1" @endif>
                <span>ابدأ المقارنة</span>
                <i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    </div>
</div>
<div class="store-compare-bar__overlay" data-compare-bar-overlay hidden></div>
