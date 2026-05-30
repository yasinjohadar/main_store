@php
    $sidebarSlides = ($sidebarProducts ?? collect())->chunk(3);
@endphp

<aside class="sidebar product-sidebar sidebar-fixed right-sidebar sticky-sidebar-wrapper store-product-sidebar">
    <div class="sidebar-overlay"></div>
    <a class="sidebar-close" href="#"><i class="close-icon"></i></a>
    <a href="#" class="sidebar-toggle d-flex d-lg-none"><i class="fas fa-chevron-right"></i></a>
    <div class="sidebar-content scrollable">
        <div class="sticky-sidebar">
            <div class="widget widget-icon-box mb-6 store-product-sidebar__features">
                <div class="icon-box icon-box-side">
                    <span class="icon-box-icon text-dark">
                        <i class="w-icon-truck"></i>
                    </span>
                    <div class="icon-box-content">
                        <h4 class="icon-box-title">شحن وإرجاع مجاني</h4>
                        <p>لجميع الطلبات فوق 200 ر.س</p>
                    </div>
                </div>
                <div class="icon-box icon-box-side">
                    <span class="icon-box-icon text-dark">
                        <i class="w-icon-bag"></i>
                    </span>
                    <div class="icon-box-content">
                        <h4 class="icon-box-title">دفع آمن</h4>
                        <p>نضمن أمان معاملاتك</p>
                    </div>
                </div>
                <div class="icon-box icon-box-side">
                    <span class="icon-box-icon text-dark">
                        <i class="w-icon-money"></i>
                    </span>
                    <div class="icon-box-content">
                        <h4 class="icon-box-title">ضمان استرداد</h4>
                        <p>خلال 30 يوماً</p>
                    </div>
                </div>
            </div>

            <div class="widget widget-banner mb-9">
                <div class="banner banner-fixed br-sm">
                    <figure>
                        <img src="{{ asset('frontend/assets/images/demos/demo-rtl/shop/banner3.jpg') }}" alt="عرض"
                            width="266" height="220" style="background-color: #1D2D44;" />
                    </figure>
                    <div class="banner-content">
                        <div class="banner-price-info font-weight-bolder text-white lh-1 ls-25">
                            40<sup class="font-weight-bold">%</sup><sub
                                class="font-weight-bold text-uppercase ls-25">خصم</sub>
                        </div>
                        <h4 class="banner-subtitle text-white font-weight-bolder text-uppercase mb-0">
                            عروض خاصة
                        </h4>
                    </div>
                </div>
            </div>

            <div class="widget widget-products">
                <div class="title-link-wrapper mb-2">
                    <h4 class="title title-link font-weight-bold">منتجات أخرى</h4>
                </div>

                <div class="swiper nav-top">
                    <div class="swiper-container swiper-theme nav-top" data-swiper-options="{
                        'slidesPerView': 1,
                        'spaceBetween': 20,
                        'navigation': {
                            'prevEl': '.swiper-button-prev',
                            'nextEl': '.swiper-button-next'
                        }
                    }">
                        <div class="swiper-wrapper">
                            @forelse($sidebarSlides as $slideProducts)
                                <div class="widget-col swiper-slide">
                                    @foreach($slideProducts as $sidebarProduct)
                                        @php
                                            $ratingPercent = (($sidebarProduct->reviews_avg_rating ?? 0) / 5) * 100;
                                        @endphp
                                        <div class="product product-widget">
                                            <figure class="product-media">
                                                <a href="{{ route('frontend.product.show', $sidebarProduct->slug) }}">
                                                    <img src="{{ $sidebarProduct->primary_image_url }}"
                                                        alt="{{ $sidebarProduct->name }}" width="100" height="113" />
                                                </a>
                                            </figure>
                                            <div class="product-details">
                                                <h4 class="product-name">
                                                    <a href="{{ route('frontend.product.show', $sidebarProduct->slug) }}">
                                                        {{ $sidebarProduct->name }}
                                                    </a>
                                                </h4>
                                                <div class="ratings-container">
                                                    <div class="ratings-full">
                                                        <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                                        <span class="tooltiptext tooltip-top"></span>
                                                    </div>
                                                </div>
                                                <div class="product-price">
                                                    {{ number_format($sidebarProduct->effective_price, 2) }} ر.س
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="widget-col swiper-slide">
                                    @for($i = 1; $i <= 3; $i++)
                                        <div class="product product-widget">
                                            <figure class="product-media">
                                                <a href="#">
                                                    <img src="{{ asset('frontend/assets/images/demos/demo-rtl/shop/' . (12 + $i) . '.jpg') }}"
                                                        alt="منتج تجريبي" width="100" height="113" />
                                                </a>
                                            </figure>
                                            <div class="product-details">
                                                <h4 class="product-name">
                                                    <a href="#">منتج تجريبي {{ $i }}</a>
                                                </h4>
                                                <div class="product-price text-muted">— ر.س</div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            @endforelse
                        </div>
                        @if($sidebarSlides->count() > 1)
                            <button class="swiper-button-next"></button>
                            <button class="swiper-button-prev"></button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>
