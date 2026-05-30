@php
    $images = $product->images->isNotEmpty()
        ? $product->images
        : collect([(object) ['url' => $product->primary_image_url, 'path' => null]]);
    $ratingPercent = (($product->reviews_avg_rating ?? 0) / 5) * 100;
    $reviewCount = $product->reviews->count();
    $maxQty = $product->track_quantity && $product->stock_quantity > 0
        ? $product->stock_quantity
        : 100;
    $productUrl = route('frontend.product.show', $product->slug);
@endphp

<div class="product product-single product-popup quick-view-modal">
    <div class="row gutter-lg">
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="quick-view-modal__gallery-wrap">
                @if($product->has_discount)
                    <span class="quick-view-modal__discount">خصم {{ $product->discount_percentage }}%</span>
                @endif

                <div class="product-gallery product-gallery-sticky quick-view-modal__gallery">
                    <div class="swiper-container product-single-swiper swiper-theme nav-inner">
                        <div class="swiper-wrapper row cols-1 gutter-no">
                            @foreach($images as $image)
                                @php
                                    $src = $image->path
                                        ? \Illuminate\Support\Facades\Storage::url($image->path)
                                        : ($image->url ?? $product->primary_image_url);
                                @endphp
                                <div class="swiper-slide">
                                    <figure class="product-image">
                                        <img src="{{ $src }}" data-zoom-image="{{ $src }}"
                                            alt="{{ $product->name }}" width="800" height="900" loading="lazy">
                                    </figure>
                                </div>
                            @endforeach
                        </div>
                        @if($images->count() > 1)
                            <button class="swiper-button-next"></button>
                            <button class="swiper-button-prev"></button>
                        @endif
                    </div>
                    @if($images->count() > 1)
                        <div class="product-thumbs-wrap swiper-container quick-view-modal__thumbs" data-swiper-options="{
                            'navigation': {
                                'nextEl': '.swiper-button-next',
                                'prevEl': '.swiper-button-prev'
                            }
                        }">
                            <div class="product-thumbs swiper-wrapper row cols-4 gutter-sm">
                                @foreach($images as $image)
                                    @php
                                        $src = $image->path
                                            ? \Illuminate\Support\Facades\Storage::url($image->path)
                                            : ($image->url ?? $product->primary_image_url);
                                    @endphp
                                    <div class="product-thumb swiper-slide">
                                        <img src="{{ $src }}" alt="{{ $product->name }}" width="103" height="116" loading="lazy">
                                    </div>
                                @endforeach
                            </div>
                            <button class="swiper-button-next"></button>
                            <button class="swiper-button-prev"></button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 p-relative">
            <div class="product-details scrollable pl-0 quick-view-modal__details">
                <span class="quick-view-modal__eyebrow">معاينة سريعة</span>

                @if($product->category)
                    <a href="{{ route('frontend.category.show', $product->category->slug) }}" class="quick-view-modal__cat">
                        {{ $product->category->name }}
                    </a>
                @endif

                <h2 class="product-title quick-view-modal__title">{{ $product->name }}</h2>

                <div class="quick-view-modal__meta">
                    @if($product->sku)
                        <span class="quick-view-modal__chip">SKU: {{ $product->sku }}</span>
                    @endif
                    <span class="quick-view-modal__chip {{ $product->in_stock ? 'is-success' : 'is-danger' }}">
                        {{ $product->in_stock ? 'متوفر' : 'غير متوفر' }}
                    </span>
                </div>

                <div class="quick-view-modal__price-box">
                    <div class="product-price quick-view-modal__price">
                        @if($product->has_discount)
                            <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                            <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                        @else
                            <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                        @endif
                    </div>
                    <div class="ratings-container quick-view-modal__rating">
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
                    <div class="product-short-desc quick-view-modal__desc">{!! $product->short_description !!}</div>
                @elseif($product->description)
                    <div class="product-short-desc quick-view-modal__desc">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 200) }}</div>
                @else
                    <p class="product-short-desc quick-view-modal__desc text-muted mb-0">وصف مختصر غير متوفر حالياً.</p>
                @endif

                @if($product->attributes->isNotEmpty())
                    @foreach($product->attributes as $attribute)
                        @if($attribute->values->isNotEmpty())
                            <div class="product-form product-variation-form product-{{ $attribute->type === 'color' ? 'color' : 'size' }}-swatch mb-3 quick-view-modal__variations">
                                <label class="mb-1">{{ $attribute->name }}:</label>
                                <div class="flex-wrap d-flex align-items-center product-variations">
                                    @foreach($attribute->values as $value)
                                        @if($attribute->type === 'color' && $value->color_hex)
                                            <a href="#" class="color" style="background-color: {{ $value->color_hex }};" title="{{ $value->value }}"></a>
                                        @else
                                            <a href="#" class="size">{{ $value->value }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif

                <div class="quick-view-modal__actions">
                    <form method="POST" action="{{ route('frontend.cart.store') }}"
                        class="product-form cart-add-form quick-view-modal__form"
                        data-product-name="{{ $product->name }}"
                        data-product-url="{{ $productUrl }}"
                        data-product-image="{{ $product->primary_image_url }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-primary btn-add-cart quick-view-modal__cart-btn" {{ $product->in_stock ? '' : 'disabled' }}>
                            <i class="w-icon-cart"></i>
                            <span>{{ $product->in_stock ? 'أضف للسلة' : 'غير متوفر' }}</span>
                        </button>
                        <div class="product-qty-form quick-view-modal__qty">
                            <label class="quick-view-modal__qty-label">الكمية</label>
                            <div class="input-group qty-input-group">
                                <button type="button" class="quantity-minus w-icon-minus"></button>
                                <input class="quantity form-control" type="number" name="quantity" min="1"
                                    max="{{ $maxQty }}" value="1">
                                <button type="button" class="quantity-plus w-icon-plus"></button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="quick-view-modal__footer">
                    <a href="{{ $productUrl }}" class="quick-view-modal__details-link">
                        عرض تفاصيل المنتج<i class="w-icon-long-arrow-left"></i>
                    </a>
                    <div class="quick-view-modal__tools">
                        <x-frontend.wishlist-button :product-id="$product->id" class="quick-view-modal__tool" />
                        <x-frontend.compare-button :product-id="$product->id" class="quick-view-modal__tool" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
