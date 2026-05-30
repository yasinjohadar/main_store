@php
    $activeCategory = $activeCategory ?? null;
    $activeTag = $activeTag ?? null;
    $totalPosts = $totalPosts ?? ($posts->total() ?? 0);
@endphp

<ul class="nav-filters filter-underline blog-filters store-blog-grid-filters mb-4">
    <li>
        <a href="{{ route('frontend.blog.index') }}"
            class="nav-filter store-blog-grid-filters__link{{ ! $activeCategory && ! $activeTag ? ' active' : '' }}">
            جميع المقالات
            <span>{{ $totalPosts }}</span>
        </a>
    </li>
    @foreach($categories as $category)
        @if(($category->posts_count ?? 0) > 0)
            <li>
                <a href="{{ route('frontend.blog.category', $category->slug) }}"
                    class="nav-filter store-blog-grid-filters__link{{ $activeCategory && $activeCategory->id === $category->id ? ' active' : '' }}">
                    {{ $category->name }}
                    <span>{{ $category->posts_count }}</span>
                </a>
            </li>
        @endif
    @endforeach
</ul>
