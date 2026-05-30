@php
    $productCount = $compareItems->count();
@endphp

<div class="page-content store-compare-page">
    <div class="container">
        <div class="store-compare-main__head">
            <div>
                <h1 class="store-compare-main__title">مقارنة المنتجات</h1>
                <p class="store-compare-main__meta">
                    @if($productCount > 0)
                        {{ $productCount }} {{ $productCount === 1 ? 'منتج' : 'منتجات' }} للمقارنة
                    @else
                        قارن بين المنتجات واختر الأنسب لك
                    @endif
                </p>
            </div>
            <div class="store-compare-main__actions">
                @if($productCount > 0)
                    <button type="button" class="store-compare-main__clear btn-compare-bar-clear">
                        <i class="fas fa-trash-alt"></i>
                        <span>مسح الكل</span>
                    </button>
                @endif
                <a href="{{ route('frontend.shop.index') }}" class="store-compare-main__shop">
                    <i class="w-icon-long-arrow-left"></i>
                    <span>متابعة التسوق</span>
                </a>
            </div>
        </div>

        @if($productCount > 0)
            <div class="store-compare-table-wrap">
                <div class="store-compare-table__top">
                    <span class="store-compare-table__top-icon"><i class="w-icon-compare"></i></span>
                    <div>
                        <strong class="store-compare-table__top-title">جدول المواصفات</strong>
                        <span class="store-compare-table__top-hint">مرّر أفقياً لمشاهدة كل المنتجات</span>
                    </div>
                </div>

                <table class="store-compare-table" style="--compare-cols: {{ $productCount }};">
                    <thead>
                        <tr class="store-compare-table__products">
                            <th class="store-compare-table__label-col" scope="col">
                                <span class="store-compare-table__label-inner store-compare-table__label-inner--head">
                                    <i class="w-icon-category"></i>
                                    <span>المنتج</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                @php $productUrl = route('frontend.product.show', $product->slug); @endphp
                                <th class="store-compare-table__product-col" scope="col" data-compare-item="{{ $product->id }}">
                                    <article class="store-compare-product">
                                        <div class="store-compare-product__media">
                                            <a href="{{ $productUrl }}" class="store-compare-product__thumb">
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                                    width="180" height="200" loading="lazy">
                                            </a>
                                            <button type="button" class="store-compare-product__remove btn-compare-bar-remove"
                                                data-delete-url="{{ route('frontend.compare.destroy', $product) }}"
                                                aria-label="إزالة {{ $product->name }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <h2 class="store-compare-product__name">
                                            <a href="{{ $productUrl }}">{{ $product->name }}</a>
                                        </h2>
                                        <div class="store-compare-product__actions">
                                            <form method="POST" action="{{ route('frontend.cart.store') }}" class="cart-add-form"
                                                data-product-name="{{ $product->name }}"
                                                data-product-url="{{ $productUrl }}"
                                                data-product-image="{{ $product->primary_image_url }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="store-compare-product__cart btn-add-cart"
                                                    {{ $product->in_stock ? '' : 'disabled' }}>
                                                    <i class="w-icon-cart"></i>
                                                    <span>أضف للسلة</span>
                                                </button>
                                            </form>
                                            <x-frontend.wishlist-button :product-id="$product->id" class="store-compare-product__wishlist" />
                                        </div>
                                    </article>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="store-compare-table__row">
                            <th class="store-compare-table__label" scope="row">
                                <span class="store-compare-table__label-inner">
                                    <i class="w-icon-money"></i>
                                    <span>السعر</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                <td class="store-compare-table__value">
                                    <div class="store-compare-table__price">
                                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                        @if($product->has_discount)
                                            <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        <tr class="store-compare-table__row store-compare-table__row--alt">
                            <th class="store-compare-table__label" scope="row">
                                <span class="store-compare-table__label-inner">
                                    <i class="w-icon-check"></i>
                                    <span>التوفر</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                <td class="store-compare-table__value">
                                    @if($product->in_stock)
                                        <span class="store-compare-table__stock store-compare-table__stock--in">
                                            <i class="w-icon-check"></i> متوفر
                                        </span>
                                    @else
                                        <span class="store-compare-table__stock store-compare-table__stock--out">
                                            <i class="fas fa-times"></i> غير متوفر
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr class="store-compare-table__row">
                            <th class="store-compare-table__label" scope="row">
                                <span class="store-compare-table__label-inner">
                                    <i class="w-icon-list"></i>
                                    <span>الوصف</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                <td class="store-compare-table__value">
                                    <p class="store-compare-table__desc mb-0">
                                        {{ Str::limit(strip_tags($product->short_description ?: $product->description), 160) ?: '—' }}
                                    </p>
                                </td>
                            @endforeach
                        </tr>
                        <tr class="store-compare-table__row store-compare-table__row--alt">
                            <th class="store-compare-table__label" scope="row">
                                <span class="store-compare-table__label-inner">
                                    <i class="w-icon-star-full"></i>
                                    <span>التقييم</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                @php
                                    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
                                    $reviewCount = $product->reviews->count();
                                @endphp
                                <td class="store-compare-table__value">
                                    <div class="store-compare-table__rating">
                                        <div class="ratings-full">
                                            <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                        </div>
                                        <a href="{{ route('frontend.product.show', $product->slug) }}" class="rating-reviews">
                                            ({{ $reviewCount }} {{ $reviewCount === 1 ? 'تقييم' : 'تقييمات' }})
                                        </a>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        <tr class="store-compare-table__row">
                            <th class="store-compare-table__label" scope="row">
                                <span class="store-compare-table__label-inner">
                                    <i class="w-icon-category"></i>
                                    <span>التصنيف</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                <td class="store-compare-table__value">
                                    @if($product->category)
                                        <a href="{{ route('frontend.category.show', $product->category->slug) }}"
                                            class="store-compare-table__cat">
                                            {{ $product->category->name }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr class="store-compare-table__row store-compare-table__row--alt">
                            <th class="store-compare-table__label" scope="row">
                                <span class="store-compare-table__label-inner">
                                    <i class="w-icon-products"></i>
                                    <span>رمز المنتج</span>
                                </span>
                            </th>
                            @foreach($compareItems as $product)
                                <td class="store-compare-table__value">
                                    <code class="store-compare-table__sku">{{ $product->sku ?? '—' }}</code>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="store-compare-page__foot d-md-none">
                <button type="button" class="store-compare-main__clear btn-compare-bar-clear store-compare-main__clear--mobile">
                    مسح الكل
                </button>
                <a href="{{ route('frontend.shop.index') }}" class="store-compare-main__shop-mobile">
                    <i class="w-icon-long-arrow-left"></i>
                    <span>متابعة التسوق</span>
                </a>
            </div>
        @else
            <div class="store-compare-empty">
                <div class="store-compare-empty__icon">
                    <i class="w-icon-compare"></i>
                </div>
                <h2 class="store-compare-empty__title">لا توجد منتجات للمقارنة</h2>
                <p class="store-compare-empty__text">أضف منتجين على الأقل من المتجر عبر أيقونة المقارنة، ثم عد هنا لمقارنة المواصفات والأسعار.</p>
                <a href="{{ route('frontend.shop.index') }}" class="store-compare-empty__btn">
                    <i class="w-icon-long-arrow-left"></i>
                    <span>تصفح المتجر</span>
                </a>
            </div>
        @endif
    </div>
</div>
