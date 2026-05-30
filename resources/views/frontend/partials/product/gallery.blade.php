@php
    $images = $product->images->isNotEmpty()
        ? $product->images
        : collect([(object) ['url' => $product->primary_image_url, 'alt' => $product->name]]);
@endphp

<div class="col-md-6 mb-4 mb-md-0">
    <div class="store-product-gallery">
        <div class="product-gallery product-gallery-sticky">
            <div class="swiper-container product-single-swiper swiper-theme nav-inner"
                data-swiper-options="{
                'navigation': {
                    'nextEl': '.swiper-button-next',
                    'prevEl': '.swiper-button-prev'
                }
            }">
                <div class="swiper-wrapper row cols-1 gutter-no">
                    @foreach($images as $image)
                        @php
                            $src = isset($image->path) ? $image->url : ($image->url ?? $product->primary_image_url);
                            $alt = $image->alt ?? $product->name;
                        @endphp
                        <div class="swiper-slide">
                            <figure class="product-image store-product-gallery__frame">
                                <img src="{{ $src }}" data-zoom-image="{{ $src }}"
                                    alt="{{ $alt }}" width="800" height="900">
                            </figure>
                        </div>
                    @endforeach
                </div>
                @if($images->count() > 1)
                    <button class="swiper-button-next"></button>
                    <button class="swiper-button-prev"></button>
                @endif
                <a href="#" class="product-gallery-btn product-image-full"><i class="w-icon-zoom"></i></a>
            </div>
            @if($images->count() > 1)
                <div class="product-thumbs-wrap swiper-container store-product-gallery__thumbs" data-swiper-options="{
                    'navigation': {
                        'nextEl': '.swiper-button-next',
                        'prevEl': '.swiper-button-prev'
                    }
                }">
                    <div class="product-thumbs swiper-wrapper row cols-4 gutter-sm">
                        @foreach($images as $image)
                            @php
                                $src = isset($image->path) ? $image->url : ($image->url ?? $product->primary_image_url);
                                $alt = $image->alt ?? $product->name;
                            @endphp
                            <div class="product-thumb swiper-slide">
                                <img src="{{ $src }}" alt="{{ $alt }}" width="800" height="900">
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
