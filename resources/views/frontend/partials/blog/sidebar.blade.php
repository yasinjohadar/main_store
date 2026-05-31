@php
    use Illuminate\Support\Facades\Storage;

    $context = $context ?? 'index';
    $sidebarFallbackImages = [
        'frontend/assets/images/blog/sidebar/1.jpg',
        'frontend/assets/images/blog/sidebar/2.jpg',
        'frontend/assets/images/blog/sidebar/3.jpg',
    ];
@endphp

<aside class="sidebar right-sidebar blog-sidebar sidebar-fixed sticky-sidebar-wrapper store-blog-sidebar{{ $context === 'show' ? ' store-blog-sidebar--show' : '' }}">
    <div class="sidebar-overlay"></div>
    <a href="#" class="sidebar-close" aria-label="إغلاق"><i class="close-icon"></i></a>
    <a href="#" class="sidebar-toggle d-lg-none" aria-label="فتح الشريط"><i class="fas fa-chevron-left"></i></a>

    <div class="sidebar-content scrollable">
        <div class="sticky-sidebar">
            @auth
                @if(auth()->user()->hasRole('admin') && isset($post))
                    <div class="store-blog-sidebar__widget store-blog-sidebar__admin">
                        <a href="{{ route('admin.blog.posts.edit', $post) }}"
                            class="store-blog-sidebar__admin-edit"
                            target="_blank"
                            rel="noopener noreferrer">
                            <span class="store-blog-sidebar__admin-edit-icon" aria-hidden="true">
                                <i class="fas fa-pen-to-square"></i>
                            </span>
                            <span class="store-blog-sidebar__admin-edit-text">
                                <strong>تعديل المقال</strong>
                                <small>يفتح في تبويب جديد</small>
                            </span>
                            <i class="w-icon-long-arrow-left store-blog-sidebar__admin-edit-arrow" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            @endauth

            <div class="store-blog-sidebar__widget store-blog-sidebar__search">
                <h3 class="store-blog-sidebar__title">
                    <span class="store-blog-sidebar__title-icon"><i class="w-icon-search"></i></span>
                    <span>بحث في المدونة</span>
                </h3>
                <form action="{{ route('frontend.blog.index') }}" method="GET" class="store-blog-sidebar__search-form">
                    <input type="text" name="search" class="form-control store-blog-sidebar__search-input"
                        placeholder="ابحث عن مقال..." value="{{ request('search') }}" autocomplete="off">
                    <button class="store-blog-sidebar__search-btn" type="submit" aria-label="بحث">
                        <i class="w-icon-search"></i>
                    </button>
                </form>
            </div>

            @if($categories->isNotEmpty())
                <div class="store-blog-sidebar__widget">
                    <h3 class="store-blog-sidebar__title">
                        <span class="store-blog-sidebar__title-icon"><i class="w-icon-category"></i></span>
                        <span>التصنيفات</span>
                    </h3>
                    <ul class="store-blog-sidebar__categories">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('frontend.blog.category', $category->slug) }}"
                                    class="store-blog-sidebar__cat-link{{ isset($post) && $post->blog_category_id === $category->id ? ' is-active' : '' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="store-blog-sidebar__cat-count">{{ $category->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($recentPosts->isNotEmpty())
                <div class="store-blog-sidebar__widget">
                    <h3 class="store-blog-sidebar__title">
                        <span class="store-blog-sidebar__title-icon"><i class="w-icon-star-full"></i></span>
                        <span>مقالات شائعة</span>
                    </h3>
                    <div class="store-blog-sidebar__posts">
                        @foreach($recentPosts->take(3) as $index => $recentPost)
                            @php
                                $thumbUrl = $recentPost->featured_image
                                    ? Storage::url($recentPost->featured_image)
                                    : asset($sidebarFallbackImages[$index % count($sidebarFallbackImages)]);
                            @endphp
                            <article class="store-blog-sidebar__post">
                                <a href="{{ route('frontend.blog.show', $recentPost->slug) }}" class="store-blog-sidebar__post-thumb">
                                    <img src="{{ $thumbUrl }}" alt="{{ $recentPost->title }}" width="80" height="80" loading="lazy">
                                </a>
                                <div class="store-blog-sidebar__post-body">
                                    <time class="store-blog-sidebar__post-date" datetime="{{ $recentPost->published_at?->toDateString() }}">
                                        {{ $recentPost->published_at?->locale('ar')->translatedFormat('j F Y') }}
                                    </time>
                                    <h4 class="store-blog-sidebar__post-title">
                                        <a href="{{ route('frontend.blog.show', $recentPost->slug) }}">{{ $recentPost->title }}</a>
                                    </h4>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($tags->isNotEmpty())
                <div class="store-blog-sidebar__widget">
                    <h3 class="store-blog-sidebar__title">
                        <span class="store-blog-sidebar__title-icon"><i class="w-icon-tag"></i></span>
                        <span>الوسوم</span>
                    </h3>
                    <div class="store-blog-sidebar__tags">
                        @foreach($tags as $tag)
                            <a href="{{ route('frontend.blog.tag', $tag->slug) }}" class="store-blog-sidebar__tag">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</aside>
