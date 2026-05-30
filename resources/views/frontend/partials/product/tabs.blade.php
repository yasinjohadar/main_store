@php
    $specs = collect([
        'اسم المنتج' => $product->name,
        'رمز SKU' => $product->sku,
        'العلامة التجارية' => $product->brand?->name,
        'التصنيف' => $product->category?->name,
        'الباركود' => $product->barcode,
        'الوزن' => $product->weight ? $product->weight . ' كجم' : null,
        'الأبعاد' => $product->dimensions,
        'الكمية المتوفرة' => $product->track_quantity ? $product->stock_quantity : null,
        'حالة التوفر' => $product->in_stock ? 'متوفر' : 'غير متوفر',
    ])->filter();

    foreach ($product->attributes as $attribute) {
        if ($attribute->values->isNotEmpty()) {
            $specs[$attribute->name] = $attribute->values->pluck('value')->join('، ');
        }
    }

    if ($specs->isEmpty()) {
        $specs = collect([
            'الموديل' => 'نموذج تجريبي — سيتم الربط لاحقاً',
            'اللون' => 'أسود',
            'المقاس' => 'متوسط، كبير',
            'فترة الضمان' => '3 أشهر',
        ]);
    }

    $vendorName = $product->brand?->name ?? 'متجر تجريبي';
    $vendorDescription = $product->brand?->description
        ?? 'هذا وصف تجريبي للبائع. سيتم ربط معلومات المتجر والعلامة التجارية من لوحة التحكم لاحقاً.';
@endphp

<div class="store-product-tabs">
<div class="tab tab-nav-boxed tab-nav-underline product-tabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a href="#product-tab-description" class="nav-link active">الوصف</a>
        </li>
        <li class="nav-item">
            <a href="#product-tab-specification" class="nav-link">المواصفات</a>
        </li>
        <li class="nav-item">
            <a href="#product-tab-vendor" class="nav-link">معلومات البائع</a>
        </li>
        <li class="nav-item">
            <a href="#product-tab-reviews" class="nav-link">التقييمات ({{ $reviews->count() }})</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="product-tab-description">
            <div class="store-product-desc">
                <div class="row gutter-lg store-product-desc__main">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="store-product-desc__content">
                            <span class="store-product-desc__eyebrow">عن المنتج</span>
                            <h4 class="store-product-desc__title">التفاصيل</h4>
                            @if($product->description)
                                <div class="store-product-desc__text">{!! nl2br(e($product->description)) !!}</div>
                            @elseif($product->short_description)
                                <p class="store-product-desc__text">{{ $product->short_description }}</p>
                            @else
                                <p class="store-product-desc__text store-product-desc__text--muted">
                                    هذا وصف تجريبي للمنتج. سيتم استبداله بالوصف الفعلي من لوحة التحكم لاحقاً.
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="store-product-desc__media">
                            <div class="banner banner-video product-video store-product-desc__video">
                                <figure class="banner-media">
                                    <a href="#">
                                        <img src="{{ asset('frontend/assets/images/demos/demo-rtl/products/video-banner-610x300.jpg') }}"
                                            alt="فيديو المنتج" width="610" height="300">
                                    </a>
                                    <a class="btn-play-video btn-iframe"
                                        href="{{ asset('frontend/assets/video/memory-of-a-woman.mp4') }}"></a>
                                </figure>
                            </div>
                            <p class="store-product-desc__video-note">* فيديو تجريبي — سيتم ربط فيديو المنتج لاحقاً</p>
                        </div>
                    </div>
                </div>

                <div class="store-product-desc__features">
                    <div class="store-product-desc__feature">
                        <span class="store-product-desc__feature-icon"><i class="w-icon-truck"></i></span>
                        <div>
                            <h5 class="store-product-desc__feature-title">شحن وإرجاع مجاني</h5>
                            <p class="store-product-desc__feature-text">شحن مجاني للطلبات فوق 200 ر.س، وإمكانية الإرجاع خلال 30 يوماً.</p>
                        </div>
                    </div>
                    <div class="store-product-desc__feature">
                        <span class="store-product-desc__feature-icon"><i class="w-icon-return"></i></span>
                        <div>
                            <h5 class="store-product-desc__feature-title">إرجاع سهل</h5>
                            <p class="store-product-desc__feature-text">ضمان استرداد كامل المبلغ خلال 30 يوماً من تاريخ الشراء.</p>
                        </div>
                    </div>
                    <div class="store-product-desc__feature">
                        <span class="store-product-desc__feature-icon"><i class="w-icon-money"></i></span>
                        <div>
                            <h5 class="store-product-desc__feature-title">دفع آمن</h5>
                            <p class="store-product-desc__feature-text">جميع المعاملات محمية بتشفير SSL لضمان أمان بياناتك.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="product-tab-specification">
            <ul class="list-none store-product-specs">
                @foreach($specs as $label => $value)
                    <li>
                        <label>{{ $label }}</label>
                        <p>{{ $value }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-pane" id="product-tab-vendor">
            <div class="row mb-3">
                <div class="col-md-6 mb-4">
                    <figure class="vendor-banner br-sm">
                        <img src="{{ asset('frontend/assets/images/demos/demo-rtl/products/vendor-banner.jpg') }}"
                            alt="بانر البائع" width="610" height="295"
                            style="background-color: #353B55;" />
                    </figure>
                </div>
                <div class="col-md-6 pl-2 pl-md-6 mb-4">
                    <div class="vendor-user">
                        <figure class="vendor-logo mr-4">
                            <img src="{{ $product->brand?->image_url ?? asset('frontend/assets/images/products/vendor-logo.jpg') }}"
                                alt="{{ $vendorName }}" width="80" height="80" />
                        </figure>
                        <div>
                            <div class="vendor-name">{{ $vendorName }}</div>
                            <div class="ratings-container">
                                <div class="ratings-full">
                                    <span class="ratings" style="width: 90%;"></span>
                                    <span class="tooltiptext tooltip-top"></span>
                                </div>
                                <span class="rating-reviews text-muted">(تقييم تجريبي)</span>
                            </div>
                        </div>
                    </div>
                    <ul class="vendor-info list-style-none">
                        <li class="store-name">
                            <label>اسم المتجر:</label>
                            <span class="detail">{{ $vendorName }}</span>
                        </li>
                        <li class="store-address">
                            <label>العنوان:</label>
                            <span class="detail">عنوان تجريبي — سيتم الربط لاحقاً</span>
                        </li>
                        <li class="store-phone">
                            <label>الهاتف:</label>
                            <span class="detail">0500000000</span>
                        </li>
                    </ul>
                    <a href="{{ route('frontend.shop.index') }}"
                        class="btn btn-dark btn-link btn-underline btn-icon-right">
                        زيارة المتجر<i class="w-icon-long-arrow-left"></i>
                    </a>
                </div>
            </div>
            <p class="mb-0">{{ $vendorDescription }}</p>
        </div>

        @include('frontend.partials.product.reviews-tab')
    </div>
</div>
</div>
