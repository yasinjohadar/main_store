@props(['post', 'fallbackImage' => null])

@php
    $imageUrl = $post->featured_image
        ? \Illuminate\Support\Facades\Storage::url($post->featured_image)
        : ($fallbackImage ?? asset('frontend/assets/images/demos/demo-rtl/blogs/1.jpg'));
    $authorName = $post->author->name ?? 'مدير النظام';
    $publishedDate = $post->published_at?->format('d.m.Y') ?? '';
@endphp

<div class="swiper-slide">
    <article class="home-blog-card">
        <a href="{{ route('frontend.blog.show', $post->slug) }}" class="home-blog-card__media">
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" loading="lazy">
            @if($post->category)
                <span class="home-blog-card__cat">{{ $post->category->name }}</span>
            @endif
        </a>
        <div class="home-blog-card__body">
            <div class="home-blog-card__meta">
                <span>{{ $authorName }}</span>
                @if($publishedDate)
                    <span class="home-blog-card__dot">·</span>
                    <time datetime="{{ $post->published_at?->toDateString() }}">{{ $publishedDate }}</time>
                @endif
            </div>
            <h3 class="home-blog-card__title">
                <a href="{{ route('frontend.blog.show', $post->slug) }}">{{ $post->title }}</a>
            </h3>
            <a href="{{ route('frontend.blog.show', $post->slug) }}" class="home-blog-card__link">
                اقرأ المزيد<i class="w-icon-long-arrow-left"></i>
            </a>
        </div>
    </article>
</div>
