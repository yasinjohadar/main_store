@php
    $activeCategory = $activeCategory ?? null;
    $activeTag = $activeTag ?? null;
    $totalPosts = $totalPosts ?? ($posts->total() ?? 0);
@endphp

<div class="store-blog-grid-filters-panel">
    <ul class="store-blog-grid-filters" role="list">
        <li class="store-blog-grid-filters__item">
            <a href="{{ route('frontend.blog.index') }}"
                class="store-blog-grid-filters__link{{ ! $activeCategory && ! $activeTag ? ' is-active' : '' }}">
                <span class="store-blog-grid-filters__label">جميع المقالات</span>
                <span class="store-blog-grid-filters__count">{{ $totalPosts }}</span>
            </a>
        </li>
        @foreach($categories as $category)
            @if(($category->posts_count ?? 0) > 0)
                <li class="store-blog-grid-filters__item">
                    <a href="{{ route('frontend.blog.category', $category->slug) }}"
                        class="store-blog-grid-filters__link{{ $activeCategory && $activeCategory->id === $category->id ? ' is-active' : '' }}">
                        <span class="store-blog-grid-filters__label">{{ $category->name }}</span>
                        <span class="store-blog-grid-filters__count">{{ $category->posts_count }}</span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
