@php
    $itemCount = $wishlistItems->count();
    $totalValue = $wishlistItems->sum(fn ($product) => $product->effective_price);
@endphp

<div class="page-content store-wishlist-page">
    <div class="container">
        @if(session('success'))
            <div class="store-cart-alert store-cart-alert--success store-wishlist-page__alert">{{ session('success') }}</div>
        @endif

        <div class="store-wishlist-main__head">
            <div>
                <h1 class="store-wishlist-main__title">المفضلة</h1>
                <p class="store-wishlist-main__meta">
                    @if($itemCount > 0)
                        {{ $itemCount }} {{ $itemCount === 1 ? 'منتج محفوظ' : 'منتجات محفوظة' }}
                    @else
                        قائمة منتجاتك المفضلة
                    @endif
                </p>
            </div>
            <a href="{{ route('frontend.shop.index') }}" class="store-wishlist-main__shop d-none d-md-inline-flex">
                <i class="w-icon-long-arrow-left"></i>
                <span>متابعة التسوق</span>
            </a>
        </div>

        @if($itemCount > 0)
            <div class="row gutter-lg store-wishlist-layout">
                <div class="col-lg-8 mb-6 store-wishlist-list-wrap">
                    <div class="store-wishlist-list">
                        @foreach($wishlistItems as $product)
                            @php
                                $productUrl = route('frontend.product.show', $product->slug);
                                $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
                            @endphp
                            <article class="store-wishlist-item" data-wishlist-item="{{ $product->id }}">
                                <a href="{{ $productUrl }}" class="store-wishlist-item__thumb">
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                        width="120" height="120" loading="lazy">
                                </a>

                                <div class="store-wishlist-item__body">
                                    @if($product->category)
                                        <a href="{{ route('frontend.category.show', $product->category->slug) }}"
                                            class="store-wishlist-item__cat">
                                            {{ $product->category->name }}
                                        </a>
                                    @endif

                                    <h2 class="store-wishlist-item__name">
                                        <a href="{{ $productUrl }}">{{ $product->name }}</a>
                                    </h2>

                                    <div class="store-wishlist-item__rating">
                                        <div class="ratings-full">
                                            <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                        </div>
                                    </div>

                                    <div class="store-wishlist-item__meta d-md-none">
                                        <div class="store-wishlist-item__price">
                                            @if($product->has_discount)
                                                <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                                <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                                            @else
                                                <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                            @endif
                                        </div>
                                        @if($product->in_stock)
                                            <span class="store-wishlist-item__stock store-wishlist-item__stock--in">متوفر</span>
                                        @else
                                            <span class="store-wishlist-item__stock store-wishlist-item__stock--out">غير متوفر</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="store-wishlist-item__price-col d-none d-md-flex">
                                    @if($product->has_discount)
                                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                        <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                                    @else
                                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                    @endif
                                </div>

                                <div class="store-wishlist-item__stock-col d-none d-md-flex">
                                    @if($product->in_stock)
                                        <span class="store-wishlist-item__stock store-wishlist-item__stock--in">
                                            <i class="w-icon-check"></i> متوفر
                                        </span>
                                    @else
                                        <span class="store-wishlist-item__stock store-wishlist-item__stock--out">
                                            <i class="fas fa-times"></i> غير متوفر
                                        </span>
                                    @endif
                                </div>

                                <div class="store-wishlist-item__actions">
                                    <form method="POST" action="{{ route('frontend.cart.store') }}" class="cart-add-form"
                                        data-product-name="{{ $product->name }}"
                                        data-product-url="{{ $productUrl }}"
                                        data-product-image="{{ $product->primary_image_url }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="store-wishlist-item__cart btn-add-cart"
                                            {{ $product->in_stock ? '' : 'disabled' }}>
                                            <i class="w-icon-cart"></i>
                                            <span>أضف للسلة</span>
                                        </button>
                                    </form>

                                    <a href="{{ route('frontend.product.quick-view', $product->slug) }}"
                                        class="store-wishlist-item__quick btn-quickview">
                                        <i class="w-icon-search"></i>
                                        <span class="d-none d-xl-inline">معاينة</span>
                                    </a>

                                    <button type="button" class="store-wishlist-item__remove btn-remove-wishlist"
                                        data-product-id="{{ $product->id }}"
                                        data-delete-url="{{ route('frontend.wishlist.destroy', $product) }}"
                                        aria-label="إزالة {{ $product->name }} من المفضلة">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <a href="{{ route('frontend.shop.index') }}" class="store-wishlist-main__shop-mobile d-md-none">
                        <i class="w-icon-long-arrow-left"></i>
                        <span>متابعة التسوق</span>
                    </a>
                </div>

                <div class="col-lg-4 mb-6">
                    <aside class="store-wishlist-summary">
                        <div class="store-wishlist-summary__head">
                            <span class="store-wishlist-summary__icon"><i class="w-icon-heart-full"></i></span>
                            <div>
                                <h2 class="store-wishlist-summary__title">ملخص المفضلة</h2>
                                <p class="store-wishlist-summary__subtitle">منتجاتك المحفوظة للشراء لاحقاً</p>
                            </div>
                        </div>

                        <div class="store-wishlist-summary__rows">
                            <div class="store-wishlist-summary__row">
                                <span>عدد المنتجات</span>
                                <strong data-wishlist-page-count>{{ $itemCount }}</strong>
                            </div>
                            <div class="store-wishlist-summary__row">
                                <span>إجمالي القيمة</span>
                                <strong data-wishlist-page-total>{{ number_format($totalValue, 2) }} ر.س</strong>
                            </div>
                        </div>

                        <a href="{{ route('frontend.shop.index') }}" class="store-wishlist-summary__cta">
                            <span>تصفح المتجر</span>
                            <i class="w-icon-long-arrow-left"></i>
                        </a>

                        <div class="store-wishlist-summary__trust">
                            <span><i class="w-icon-check"></i> احفظ ما يعجبك</span>
                            <span><i class="w-icon-check"></i> اشترِ لاحقاً بسهولة</span>
                        </div>
                    </aside>
                </div>
            </div>
        @else
            <div class="store-wishlist-empty">
                <div class="store-wishlist-empty__icon">
                    <i class="w-icon-heart"></i>
                </div>
                <h2 class="store-wishlist-empty__title">المفضلة فارغة</h2>
                <p class="store-wishlist-empty__text">لم تضف أي منتجات بعد. تصفّح المتجر واضغط على أيقونة القلب لحفظ ما يعجبك.</p>
                <a href="{{ route('frontend.shop.index') }}" class="store-wishlist-empty__btn">
                    <i class="w-icon-long-arrow-left"></i>
                    <span>ابدأ التسوق</span>
                </a>
            </div>
        @endif
    </div>
</div>
