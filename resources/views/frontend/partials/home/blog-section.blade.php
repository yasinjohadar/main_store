@php
    $fallbackBlogImages = [
        'frontend/assets/images/demos/demo-rtl/blogs/1.jpg',
        'frontend/assets/images/demos/demo-rtl/blogs/2.jpg',
        'frontend/assets/images/demos/demo-rtl/blogs/3.jpg',
        'frontend/assets/images/demos/demo-rtl/blogs/4.jpg',
    ];
    $blogEyebrow = $section->resolvedEyebrow($sectionDefaults['eyebrow'] ?? 'المدونة');
    $blogTitle = $section->resolvedTitle($sectionDefaults['title'] ?? 'من مدونتنا');
    $viewAllLabel = $section->getSetting('view_all_label', $sectionDefaults['settings']['view_all_label'] ?? 'عرض جميع المقالات');
@endphp

<section class="home-blog appear-animate mb-4">
    <div class="home-blog__header">
        <div class="home-blog__heading">
            @if($blogEyebrow)
                <span class="home-blog__eyebrow">{{ $blogEyebrow }}</span>
            @endif
            <h2 class="home-blog__title">{{ $blogTitle }}</h2>
        </div>
        <a href="{{ route('frontend.blog.index') }}" class="home-blog__view-all">
            {{ $viewAllLabel }}
            <i class="w-icon-long-arrow-left"></i>
        </a>
    </div>

    <div class="swiper home-blog__swiper">
        <div class="swiper-container swiper-theme" data-swiper-options="{
            'slidesPerView': 1,
            'spaceBetween': 20,
            'breakpoints': {
                '576': { 'slidesPerView': 2, 'spaceBetween': 20 },
                '768': { 'slidesPerView': 3, 'spaceBetween': 22 },
                '992': { 'slidesPerView': 4, 'spaceBetween': 24 }
            }
        }">
            <div class="swiper-wrapper">
                @forelse($blogPosts ?? [] as $index => $post)
                    <x-frontend.blog-slide
                        :post="$post"
                        :fallback-image="asset($fallbackBlogImages[$index % count($fallbackBlogImages)])"
                    />
                @empty
                    @foreach([
                        ['title' => 'دليلك الشامل لتعلم Laravel', 'image' => $fallbackBlogImages[0]],
                        ['title' => 'أفضل طرق تسويق المتجر الإلكتروني', 'image' => $fallbackBlogImages[1]],
                        ['title' => 'كيف تختار المنتجات المناسبة', 'image' => $fallbackBlogImages[2]],
                        ['title' => 'نصائح للتسوق الذكي عبر الإنترنت', 'image' => $fallbackBlogImages[3]],
                    ] as $item)
                        <div class="swiper-slide">
                            <article class="home-blog-card">
                                <a href="{{ route('frontend.blog.index') }}" class="home-blog-card__media">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['title'] }}" loading="lazy">
                                </a>
                                <div class="home-blog-card__body">
                                    <div class="home-blog-card__meta">
                                        <span>مدير النظام</span>
                                        <span class="home-blog-card__dot">·</span>
                                        <time>07.05.2026</time>
                                    </div>
                                    <h3 class="home-blog-card__title">
                                        <a href="{{ route('frontend.blog.index') }}">{{ $item['title'] }}</a>
                                    </h3>
                                    <a href="{{ route('frontend.blog.index') }}" class="home-blog-card__link">
                                        اقرأ المزيد<i class="w-icon-long-arrow-left"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </div>
</section>
