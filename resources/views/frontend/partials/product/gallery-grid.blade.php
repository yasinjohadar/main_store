@php
    $images = $product->images->isNotEmpty()
        ? $product->images
        : collect([(object) ['url' => $product->primary_image_url, 'alt' => $product->name]]);
@endphp

<div class="col-md-6 mb-4 mb-md-0">
    <div class="store-product-gallery store-product-gallery--grid">
        <div class="product-gallery store-product-gallery-grid">
            <div class="store-product-gallery-grid__main">
                @php
                    $main = $images->first();
                    $mainSrc = isset($main->path) ? $main->url : ($main->url ?? $product->primary_image_url);
                @endphp
                <figure class="product-image store-product-gallery-grid__hero store-product-gallery__frame">
                    <img src="{{ $mainSrc }}" alt="{{ $product->name }}" width="800" height="900" id="product-gallery-main">
                </figure>
            </div>

            @if($images->count() > 1)
                <div class="store-product-gallery-grid__thumbs row cols-4 gutter-sm store-product-gallery__thumbs">
                    @foreach($images as $index => $image)
                        @php
                            $src = isset($image->path) ? $image->url : ($image->url ?? $product->primary_image_url);
                        @endphp
                        <button type="button" class="store-product-gallery-grid__thumb{{ $index === 0 ? ' is-active' : '' }}"
                            data-src="{{ $src }}" aria-label="صورة {{ $index + 1 }}">
                            <img src="{{ $src }}" alt="{{ $image->alt ?? $product->name }}" width="120" height="120" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.store-product-gallery-grid__thumb').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var src = this.getAttribute('data-src');
                    var main = document.getElementById('product-gallery-main');
                    if (main && src) {
                        main.src = src;
                    }
                    document.querySelectorAll('.store-product-gallery-grid__thumb').forEach(function (el) {
                        el.classList.remove('is-active');
                    });
                    this.classList.add('is-active');
                });
            });
        });
    </script>
@endpush
