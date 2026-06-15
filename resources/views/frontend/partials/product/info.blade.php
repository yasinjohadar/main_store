@php
    $reviewCount = $reviews->count();
    $avgRating = $reviewCount > 0 ? $reviews->avg('rating') : 0;
    $ratingPercent = $reviewCount > 0 ? ($avgRating / 5) * 100 : 0;
    $maxQty = $product->track_quantity && $product->stock_quantity > 0
        ? $product->stock_quantity
        : 100;
@endphp

<div class="col-md-6 mb-4 mb-md-0">
    <div class="store-product-info">
        <div class="product-details" data-sticky-options="{'minWidth': 767}">
            <div class="store-product-info__head">
                <h2 class="product-title">{{ $product->name }}</h2>
                @auth
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="store-product-info__admin-edit"
                            target="_blank"
                            rel="noopener noreferrer">
                            <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                            <span>تعديل المنتج</span>
                        </a>
                    @endif
                @endauth
            </div>

            <div class="store-product-info__meta">
                @if($product->brand)
                    <figure class="brand store-product-info__brand">
                        <img src="{{ $product->brand->image_url ?? asset('frontend/assets/images/products/brand/brand-1.jpg') }}"
                            alt="{{ $product->brand->name }}" width="102" height="48" />
                    </figure>
                @endif
                <div class="store-product-info__chips">
                    @if($product->category)
                        <span class="store-product-info__chip">
                            <a href="{{ route('frontend.category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                        </span>
                    @endif
                    @if($product->sku)
                        <span class="store-product-info__chip">SKU: <span data-product-sku>{{ $product->sku }}</span></span>
                    @endif
                    <span class="store-product-info__chip store-product-info__chip--{{ $product->in_stock ? 'success' : 'danger' }}">
                        {{ $product->in_stock ? 'متوفر' : 'غير متوفر' }}
                    </span>
                    @if($product->has_discount)
                        <span class="store-product-info__chip store-product-info__chip--sale">خصم</span>
                    @endif
                </div>
            </div>

            <hr class="product-divider">

            <div class="product-price store-product-info__price">
                @if($product->has_discount)
                    <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                    <del class="old-price">{{ number_format($product->compare_at_price, 2) }} ر.س</del>
                @else
                    <ins class="new-price">{{ number_format($product->effective_price, 2) }} ر.س</ins>
                @endif
            </div>

            <div class="store-product-info__rating">
                <div class="ratings-container">
                    <div class="ratings-full">
                        <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                        <span class="tooltiptext tooltip-top"></span>
                    </div>
                    <a href="#product-tab-reviews" class="rating-reviews">
                        ({{ $reviewCount }} {{ $reviewCount === 1 ? 'تقييم' : 'تقييمات' }})
                    </a>
                </div>
            </div>

            @if($product->short_description)
                <div class="product-short-desc">{!! $product->short_description !!}</div>
            @else
                <p class="product-short-desc text-muted">
                    وصف مختصر تجريبي — سيتم استبداله بالوصف الفعلي من لوحة التحكم لاحقاً.
                </p>
            @endif

            <hr class="product-divider">

            @php
                $variantOptions = $variantOptions ?? $product->variantOptions();
                $variantsJson = $variantsJson ?? app(\App\Services\ProductVariantOptionService::class)->variantsPayload($product);
            @endphp

            @include('frontend.partials.product.attribute-options', compact('variantOptions'))

            <form method="POST" action="{{ route('frontend.cart.store') }}"
                class="product-form product-variation-form cart-add-form store-product-purchase"
                data-product-name="{{ $product->name }}"
                data-product-url="{{ route('frontend.product.show', $product->slug) }}"
                data-product-image="{{ $product->primary_image_url }}"
                @if(! empty($variantsJson)) data-variants='@json($variantsJson)' @endif>
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variant_id" id="product-variant-id" value="">
                <div class="store-product-purchase__row">
                    <div class="store-product-purchase__qty">
                        <label>الكمية:</label>
                        <div class="product-qty-form">
                            <div class="input-group qty-input-group store-cart-item__qty-group">
                                <button type="button" class="quantity-minus w-icon-minus"></button>
                                <input class="quantity form-control" type="number" name="quantity" min="1"
                                    max="{{ $maxQty }}" value="1">
                                <button type="button" class="quantity-plus w-icon-plus"></button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-add-cart store-product-purchase__submit"
                        data-analytics-add-cart
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-price="{{ $product->price }}"
                        {{ $product->in_stock ? '' : 'disabled' }}>
                        <i class="w-icon-cart"></i>
                        <span>{{ $product->in_stock ? 'أضف للسلة' : 'غير متوفر' }}</span>
                    </button>
                </div>
            </form>

            @if(! $product->in_stock && $product->track_quantity)
                <form method="POST" action="{{ route('frontend.products.notify-stock', $product) }}" class="mt-3 store-product-notify">
                    @csrf
                    @guest
                        <input type="email" name="email" class="form-control mb-2" placeholder="بريدك الإلكتروني" required>
                    @endguest
                    <button type="submit" class="btn btn-outline-primary btn-sm">أبلغني عند التوفر</button>
                </form>
            @endif

            <div class="social-links-wrapper store-product-info__actions">
                <div class="social-links">
                    <div class="social-icons social-no-color border-thin">
                        <a href="#" class="social-icon social-facebook w-icon-facebook"></a>
                        <a href="#" class="social-icon social-twitter w-icon-twitter"></a>
                        <a href="#" class="social-icon social-pinterest fab fa-pinterest-p"></a>
                        <a href="#" class="social-icon social-whatsapp fab fa-whatsapp"></a>
                    </div>
                </div>
                <span class="divider d-xs-show"></span>
                <div class="product-link-wrapper d-flex">
                    <x-frontend.wishlist-button :product-id="$product->id" />
                    <x-frontend.compare-button :product-id="$product->id" class="btn-icon-left" />
                </div>
            </div>
        </div>
    </div>
</div>
