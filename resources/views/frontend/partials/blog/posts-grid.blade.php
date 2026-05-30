@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $fallbackImages = [
        'frontend/assets/images/blog/classic/1.jpg',
        'frontend/assets/images/blog/classic/2.jpg',
        'frontend/assets/images/blog/classic/3.jpg',
        'frontend/assets/images/blog/classic/4.jpg',
        'frontend/assets/images/blog/classic/5.jpg',
        'frontend/assets/images/blog/classic/6.jpg',
        'frontend/assets/images/blog/classic/5.jpg',
        'frontend/assets/images/blog/classic/6.jpg',
    ];
@endphp

<div class="store-blog-grid mb-2">
    @forelse($posts as $index => $post)
        @php
            $imageUrl = $post->featured_image
                ? Storage::url($post->featured_image)
                : asset($fallbackImages[$index % count($fallbackImages)]);
            $authorName = $post->author->name ?? 'المحرر';
            $publishedDate = $post->published_at?->locale('ar')->translatedFormat('d.m.Y') ?? '';
            $excerpt = $post->excerpt ?: Str::limit(strip_tags($post->content), 120);
        @endphp
        <article class="post post-grid-type store-blog-grid__item">
            <figure class="post-media br-sm store-blog-grid__media overlay-zoom">
                <a href="{{ route('frontend.blog.show', $post->slug) }}">
                    <img src="{{ $imageUrl }}" width="600" height="420" alt="{{ $post->title }}" loading="lazy">
                </a>
            </figure>
            <div class="post-details store-blog-grid__body">
                @if($post->category)
                    <div class="post-cats text-primary store-blog-grid__cats">
                        <a href="{{ route('frontend.blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                    </div>
                @endif
                <h4 class="post-title store-blog-grid__title">
                    <a href="{{ route('frontend.blog.show', $post->slug) }}">{{ $post->title }}</a>
                </h4>
                <div class="post-content store-blog-grid__excerpt">
                    <p>{{ $excerpt }}</p>
                    <a href="{{ route('frontend.blog.show', $post->slug) }}" class="btn btn-link btn-primary store-blog-grid__read-more">اقرأ المزيد</a>
                </div>
                <div class="post-meta store-blog-grid__meta">
                    <span class="store-blog-grid__meta-by">بواسطة</span>
                    <a href="#" class="post-author">{{ $authorName }}</a>
                    @if($publishedDate)
                        <span class="store-blog-grid__meta-sep">-</span>
                        <time class="post-date" datetime="{{ $post->published_at?->toDateString() }}">{{ $publishedDate }}</time>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="col-12 store-blog-grid__empty">
            <div class="store-blog-grid__empty-inner">
                <i class="w-icon-doc"></i>
                <p>لم يتم العثور على مقالات.</p>
                <a href="{{ route('frontend.blog.index') }}" class="btn btn-primary btn-rounded">عرض جميع المقالات</a>
            </div>
        </div>
    @endforelse
</div>

@if($posts->hasPages())
    @include('frontend.partials.blog.pagination', ['paginator' => $posts])
@endif
