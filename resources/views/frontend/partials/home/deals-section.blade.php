@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="row home-deals appear-animate mb-8">
    <div class="col-lg-9 mb-4 mb-lg-0">
        <div class="home-deals-card h-100">
            <div class="home-deals-card__header">
                <div class="home-deals-card__heading">
                    <span class="home-deals-card__eyebrow">عروض محدودة</span>
                    <h4 class="home-deals-card__title">عروض اليوم الساخنة</h4>
                </div>
                @if($dealProducts->count() > 1)
                    <div class="home-deals-card__nav">
                        <button type="button" class="home-deals-card__nav-btn home-deals-card__nav-btn--prev" aria-label="العرض السابق">
                            <i class="w-icon-long-arrow-right"></i>
                        </button>
                        <button type="button" class="home-deals-card__nav-btn home-deals-card__nav-btn--next" aria-label="العرض التالي">
                            <i class="w-icon-long-arrow-left"></i>
                        </button>
                    </div>
                @endif
            </div>

            @if($dealProducts->isNotEmpty())
                <div class="swiper home-deals-card__swiper">
                    <div class="swiper-container swiper-theme" data-swiper-options="{
                        'spaceBetween': 24,
                        'slidesPerView': 1,
                        'navigation': {
                            'nextEl': '.home-deals-card__nav-btn--next',
                            'prevEl': '.home-deals-card__nav-btn--prev'
                        }
                    }">
                        <div class="swiper-wrapper">
                            @foreach($dealProducts as $product)
                                @php
                                    $productUrl = route('frontend.product.show', $product->slug);
                                    $images = $product->images->isNotEmpty()
                                        ? $product->images
                                        : collect([(object) ['path' => null, 'url' => $product->primary_image_url]]);
                                    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
                                    $reviewCount = $product->reviews->count();
                                    $maxQty = $product->track_quantity && $product->stock_quantity > 0
                                        ? $product->stock_quantity
                                        : 100;
                                @endphp
                                <div class="swiper-slide">
                                    <article class="home-deal-product">
                                        <div class="home-deal-product__gallery">
                                            <a href="{{ $productUrl }}" class="home-deal-product__image-link">
                                                @php
                                                    $primary = $images->first();
                                                    $imgUrl = $primary->path
                                                        ? Storage::url($primary->path)
                                                        : $product->primary_image_url;
                                                @endphp
                                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" loading="lazy">
                                            </a>
                                            @if($product->has_discount)
                                                <span class="home-deal-product__badge">
                                                    خصم {{ $product->discount_percentage }}%
                                                </span>
                                            @endif
                                            <span class="home-deal-product__shine" aria-hidden="true"></span>
                                        </div>

                                        <div class="home-deal-product__body">
                                            @if($product->category)
                                                <a href="{{ route('frontend.category.show', $product->category->slug) }}"
                                                    class="home-deal-product__cat">
                                                    {{ $product->category->name }}
                                                </a>
                                            @endif

                                            <h3 class="home-deal-product__name">
                                                <a href="{{ $productUrl }}">{{ $product->name }}</a>
                                            </h3>

                                            <div class="home-deal-product__price-box">
                                                <div class="home-deal-product__price">
                                                    @if($product->has_discount)
                                                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                                        <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                                                    @else
                                                        <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                                    @endif
                                                </div>
                                                <div class="ratings-container home-deal-product__rating">
                                                    <div class="ratings-full">
                                                        <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                                        <span class="tooltiptext tooltip-top"></span>
                                                    </div>
                                                    <a href="{{ $productUrl }}" class="rating-reviews">
                                                        ({{ $reviewCount }} {{ $reviewCount === 1 ? 'تقييم' : 'تقييمات' }})
                                                    </a>
                                                </div>
                                            </div>

                                            @if($product->short_description)
                                                <p class="home-deal-product__desc">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 160) }}
                                                </p>
                                            @endif

                                            <div class="home-deal-product__actions">
                                                <form method="POST" action="{{ route('frontend.cart.store') }}"
                                                    class="product-form cart-add-form home-deal-product__form"
                                                    data-product-name="{{ $product->name }}"
                                                    data-product-url="{{ $productUrl }}"
                                                    data-product-image="{{ $product->primary_image_url }}">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <button type="submit" class="btn btn-primary btn-add-cart home-deal-product__cart-btn" {{ $product->in_stock ? '' : 'disabled' }}>
                                                        <i class="w-icon-cart"></i>
                                                        <span>{{ $product->in_stock ? 'أضف للسلة' : 'غير متوفر' }}</span>
                                                    </button>
                                                    <div class="product-qty-form home-deal-product__qty">
                                                        <label class="home-deal-product__qty-label">الكمية</label>
                                                        <div class="input-group qty-input-group">
                                                            <button type="button" class="quantity-minus w-icon-minus"></button>
                                                            <input class="quantity form-control" type="number" name="quantity"
                                                                min="1" max="{{ $maxQty }}" value="1">
                                                            <button type="button" class="quantity-plus w-icon-plus"></button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <a href="{{ $productUrl }}" class="home-deal-product__details-link">
                                                عرض تفاصيل المنتج<i class="w-icon-long-arrow-left"></i>
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="home-deals-card__empty">
                    <span class="home-deals-card__empty-icon"><i class="w-icon-sale"></i></span>
                    <p>لا توجد عروض متاحة حالياً.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-3">
        <aside class="home-bestsellers-card h-100">
            <div class="home-bestsellers-card__header">
                <span class="home-bestsellers-card__eyebrow">الأكثر طلباً</span>
                <h4 class="home-bestsellers-card__title">الأكثر مبيعاً</h4>
            </div>

            @if($bestSellers->isNotEmpty())
                <div class="swiper home-bestsellers-card__swiper">
                    <div class="swiper-container swiper-theme" data-swiper-options="{
                        'slidesPerView': 1,
                        'spaceBetween': 16,
                        'navigation': {
                            'nextEl': '.home-bestsellers-card__nav-btn--next',
                            'prevEl': '.home-bestsellers-card__nav-btn--prev'
                        }
                    }">
                        <div class="swiper-wrapper">
                            @foreach($bestSellers->chunk(3) as $groupIndex => $group)
                                <div class="swiper-slide">
                                    <ul class="home-bestsellers-list">
                                        @foreach($group as $product)
                                            @php
                                                $productUrl = route('frontend.product.show', $product->slug);
                                                $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
                                                $rank = ($groupIndex * 3) + $loop->iteration;
                                            @endphp
                                            <li class="home-bestsellers-item">
                                                <a href="{{ $productUrl }}" class="home-bestsellers-item__thumb">
                                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" loading="lazy">
                                                    <span class="home-bestsellers-item__rank">{{ $rank }}</span>
                                                </a>
                                                <div class="home-bestsellers-item__body">
                                                    <h5 class="home-bestsellers-item__name">
                                                        <a href="{{ $productUrl }}">{{ $product->name }}</a>
                                                    </h5>
                                                    <div class="ratings-container home-bestsellers-item__rating">
                                                        <div class="ratings-full">
                                                            <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="home-bestsellers-item__price">
                                                        @if($product->has_discount)
                                                            <ins>{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                                            <del>{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                                                        @else
                                                            <ins>{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($bestSellers->count() > 3)
                    <div class="home-bestsellers-card__nav">
                        <button type="button" class="home-bestsellers-card__nav-btn home-bestsellers-card__nav-btn--prev" aria-label="السابق">
                            <i class="w-icon-long-arrow-right"></i>
                        </button>
                        <button type="button" class="home-bestsellers-card__nav-btn home-bestsellers-card__nav-btn--next" aria-label="التالي">
                            <i class="w-icon-long-arrow-left"></i>
                        </button>
                    </div>
                @endif
            @else
                <p class="home-bestsellers-card__empty mb-0">لا توجد منتجات.</p>
            @endif
        </aside>
    </div>
</div>
