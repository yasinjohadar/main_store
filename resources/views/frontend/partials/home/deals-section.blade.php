@php
    use Illuminate\Support\Facades\Storage;

    $dealsEyebrow = $section->resolvedEyebrow($sectionDefaults['eyebrow'] ?? 'عروض محدودة');
    $dealsTitle = $section->resolvedTitle($sectionDefaults['title'] ?? 'عروض اليوم الساخنة');
    $bestsellersEyebrow = $section->getSetting('bestsellers_eyebrow', $sectionDefaults['settings']['bestsellers_eyebrow'] ?? 'الأكثر طلباً');
    $bestsellersTitle = $section->getSetting('bestsellers_title', $sectionDefaults['settings']['bestsellers_title'] ?? 'الأكثر مبيعاً');
    $bestsellersPerSlide = 4;
@endphp

<div class="row home-deals appear-animate mb-8">
    <div class="col-lg-9 mb-4 mb-lg-0">
        <div class="home-deals-card h-100">
            <div class="home-deals-card__header">
                <div class="home-deals-card__heading">
                    <span class="home-deals-card__icon" aria-hidden="true"><i class="w-icon-sale"></i></span>
                    <div>
                        @if($dealsEyebrow)
                            <span class="home-deals-card__eyebrow">{{ $dealsEyebrow }}</span>
                        @endif
                        <h4 class="home-deals-card__title">{{ $dealsTitle }}</h4>
                    </div>
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
                                    $imageUrls = $product->images
                                        ->map(fn ($image) => $image->path ? Storage::url($image->path) : null)
                                        ->filter()
                                        ->values();
                                    if ($imageUrls->isEmpty()) {
                                        $imageUrls = collect([$product->primary_image_url]);
                                    }
                                    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
                                    $reviewCount = $product->reviews->count();
                                    $savings = $product->has_discount
                                        ? $product->compare_at_price - $product->effective_price
                                        : 0;
                                    $lowStock = $product->in_stock && $product->track_quantity && $product->stock_quantity <= 10;
                                    $maxQty = $product->track_quantity && $product->stock_quantity > 0
                                        ? $product->stock_quantity
                                        : 100;
                                @endphp
                                <div class="swiper-slide">
                                    <article class="home-deal-product">
                                        <div class="home-deal-product__media">
                                            <div class="home-deal-product__gallery">
                                                <a href="{{ $productUrl }}" class="home-deal-product__image-link">
                                                    <img src="{{ $imageUrls->first() }}" alt="{{ $product->name }}" loading="lazy" class="home-deal-product__main-img">
                                                </a>
                                                @if($product->has_discount)
                                                    <span class="home-deal-product__badge">
                                                        <strong>{{ $product->discount_percentage }}%</strong>
                                                        <small>خصم</small>
                                                    </span>
                                                @endif
                                                <span class="home-deal-product__shine" aria-hidden="true"></span>
                                            </div>

                                            @if($imageUrls->count() > 1)
                                                <div class="home-deal-product__thumbs">
                                                    @foreach($imageUrls->take(4) as $thumbUrl)
                                                        <button type="button"
                                                            class="home-deal-product__thumb{{ $loop->first ? ' is-active' : '' }}"
                                                            data-image="{{ $thumbUrl }}"
                                                            aria-label="عرض الصورة {{ $loop->iteration }}">
                                                            <img src="{{ $thumbUrl }}" alt="" loading="lazy">
                                                        </button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <div class="home-deal-product__body">
                                            <div class="home-deal-product__meta">
                                                @if($product->category)
                                                    <a href="{{ route('frontend.category.show', $product->category->slug) }}"
                                                        class="home-deal-product__cat">
                                                        {{ $product->category->name }}
                                                    </a>
                                                @endif
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

                                            <h3 class="home-deal-product__name">
                                                <a href="{{ $productUrl }}">{{ $product->name }}</a>
                                            </h3>

                                            @if($product->short_description)
                                                <p class="home-deal-product__desc">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 140) }}
                                                </p>
                                            @endif

                                            <div class="home-deal-product__price">
                                                <ins class="new-price">
                                                    {{ number_format($product->effective_price, 2) }}
                                                    <small>ر.س</small>
                                                </ins>
                                                @if($product->has_discount)
                                                    <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                                                    <span class="home-deal-product__save">وفّر {{ number_format($savings, 2) }} ر.س</span>
                                                @endif
                                            </div>

                                            <div class="home-deal-product__stock{{ ! $product->in_stock ? ' is-out' : ($lowStock ? ' is-low' : '') }}">
                                                <span class="home-deal-product__stock-dot" aria-hidden="true"></span>
                                                @if(! $product->in_stock)
                                                    نفد من المخزون
                                                @elseif($lowStock)
                                                    متبقي {{ $product->stock_quantity }} {{ $product->stock_quantity <= 2 ? 'قطعة' : 'قطع' }} فقط — اطلبه الآن
                                                @else
                                                    متوفر في المخزون وجاهز للشحن
                                                @endif
                                            </div>

                                            <form method="POST" action="{{ route('frontend.cart.store') }}"
                                                class="product-form cart-add-form home-deal-product__form"
                                                data-product-name="{{ $product->name }}"
                                                data-product-url="{{ $productUrl }}"
                                                data-product-image="{{ $product->primary_image_url }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <div class="product-qty-form home-deal-product__qty">
                                                    <div class="input-group qty-input-group">
                                                        <button type="button" class="quantity-minus w-icon-minus" aria-label="إنقاص الكمية"></button>
                                                        <input class="quantity form-control" type="number" name="quantity"
                                                            min="1" max="{{ $maxQty }}" value="1" aria-label="الكمية">
                                                        <button type="button" class="quantity-plus w-icon-plus" aria-label="زيادة الكمية"></button>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-add-cart home-deal-product__cart-btn" {{ $product->in_stock ? '' : 'disabled' }}>
                                                    <i class="w-icon-cart"></i>
                                                    <span>{{ $product->in_stock ? 'أضف للسلة' : 'غير متوفر' }}</span>
                                                </button>
                                            </form>

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
                <div>
                    @if($bestsellersEyebrow)
                        <span class="home-bestsellers-card__eyebrow">{{ $bestsellersEyebrow }}</span>
                    @endif
                    <h4 class="home-bestsellers-card__title">{{ $bestsellersTitle }}</h4>
                </div>
                @if($bestSellers->count() > $bestsellersPerSlide)
                    <div class="home-bestsellers-card__nav">
                        <button type="button" class="home-bestsellers-card__nav-btn home-bestsellers-card__nav-btn--prev" aria-label="السابق">
                            <i class="w-icon-long-arrow-right"></i>
                        </button>
                        <button type="button" class="home-bestsellers-card__nav-btn home-bestsellers-card__nav-btn--next" aria-label="التالي">
                            <i class="w-icon-long-arrow-left"></i>
                        </button>
                    </div>
                @endif
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
                            @foreach($bestSellers->chunk($bestsellersPerSlide) as $groupIndex => $group)
                                <div class="swiper-slide">
                                    <ul class="home-bestsellers-list">
                                        @foreach($group as $product)
                                            @php
                                                $productUrl = route('frontend.product.show', $product->slug);
                                                $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
                                                $rank = ($groupIndex * $bestsellersPerSlide) + $loop->iteration;
                                                $soldCount = (int) ($product->sold_count ?? 0);
                                            @endphp
                                            <li class="home-bestsellers-item">
                                                <a href="{{ $productUrl }}" class="home-bestsellers-item__thumb">
                                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" loading="lazy">
                                                </a>
                                                <div class="home-bestsellers-item__body">
                                                    <h5 class="home-bestsellers-item__name">
                                                        <a href="{{ $productUrl }}">{{ $product->name }}</a>
                                                    </h5>
                                                    <div class="ratings-container home-bestsellers-item__rating">
                                                        <div class="ratings-full">
                                                            <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                                        </div>
                                                        @if($soldCount > 0)
                                                            <span class="home-bestsellers-item__sold">· بيع {{ $soldCount }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="home-bestsellers-item__price">
                                                        <ins>{{ number_format($product->effective_price, 2) }} ر.س</ins>
                                                        @if($product->has_discount)
                                                            <del>{{ number_format($product->compare_at_price, 2) }}</del>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="home-bestsellers-item__rank{{ $rank <= 3 ? ' is-top is-top-' . $rank : '' }}">{{ $rank }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="home-bestsellers-card__footer">
                    <a href="{{ route('frontend.shop.index', ['sort' => 'popularity']) }}" class="home-bestsellers-card__all">
                        تصفح كل المنتجات<i class="w-icon-long-arrow-left"></i>
                    </a>
                </div>
            @else
                <p class="home-bestsellers-card__empty mb-0">لا توجد منتجات.</p>
            @endif
        </aside>
    </div>
</div>

@once
    <script>
        document.addEventListener('click', function (event) {
            var thumb = event.target.closest('.home-deal-product__thumb');
            if (!thumb) return;

            var product = thumb.closest('.home-deal-product');
            product.querySelector('.home-deal-product__main-img').src = thumb.dataset.image;
            product.querySelectorAll('.home-deal-product__thumb').forEach(function (item) {
                item.classList.toggle('is-active', item === thumb);
            });
        });
    </script>
@endonce
