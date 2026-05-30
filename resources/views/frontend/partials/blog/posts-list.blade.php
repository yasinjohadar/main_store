@php
    use Illuminate\Support\Facades\Storage;

    $fallbackImages = [
        'frontend/assets/images/blog/classic/1.jpg',
        'frontend/assets/images/blog/classic/2.jpg',
        'frontend/assets/images/blog/classic/3.jpg',
        'frontend/assets/images/blog/classic/4.jpg',
        'frontend/assets/images/blog/classic/5.jpg',
        'frontend/assets/images/blog/classic/6.jpg',
    ];
@endphp

<div class="main-content">
    @forelse($posts as $index => $post)
        @php
            $imageUrl = $post->featured_image
                ? Storage::url($post->featured_image)
                : asset($fallbackImages[$index % count($fallbackImages)]);
            $authorName = $post->author->name ?? 'Admin';
            $publishedDate = $post->published_at?->format('d.m.Y') ?? '';
        @endphp
        <article class="post post-classic overlay-zoom mb-4">
            <figure class="post-media br-sm">
                <a href="{{ route('frontend.blog.show', $post->slug) }}">
                    <img src="{{ $imageUrl }}" width="930" height="500" alt="{{ $post->title }}">
                </a>
            </figure>
            <div class="post-details">
                @if($post->category)
                    <div class="post-cats text-primary">
                        <a href="{{ route('frontend.blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                    </div>
                @endif
                <h4 class="post-title">
                    <a href="{{ route('frontend.blog.show', $post->slug) }}">{{ $post->title }}</a>
                </h4>
                <div class="post-content">
                    <p>{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 220) }}</p>
                    <a href="{{ route('frontend.blog.show', $post->slug) }}" class="btn btn-link btn-primary">(read more)</a>
                </div>
                <div class="post-meta">
                    by <a href="#" class="post-author">{{ $authorName }}</a>
                    @if($publishedDate)
                        - <a href="#" class="post-date">{{ $publishedDate }}</a>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="text-center py-10">
            <p class="mb-0">No blog posts found.</p>
        </div>
    @endforelse

    @if($posts->hasPages())
        <ul class="pagination justify-content-center pb-2">
            <li class="prev {{ $posts->onFirstPage() ? 'disabled' : '' }}">
                @if($posts->onFirstPage())
                    <a href="#" aria-label="Previous" tabindex="-1" aria-disabled="true">
                        <i class="w-icon-long-arrow-left"></i>Prev
                    </a>
                @else
                    <a href="{{ $posts->previousPageUrl() }}" aria-label="Previous">
                        <i class="w-icon-long-arrow-left"></i>Prev
                    </a>
                @endif
            </li>

            @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                <li class="page-item {{ $page == $posts->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            <li class="next {{ $posts->hasMorePages() ? '' : 'disabled' }}">
                @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" aria-label="Next">
                        Next<i class="w-icon-long-arrow-right"></i>
                    </a>
                @else
                    <a href="#" aria-label="Next" tabindex="-1" aria-disabled="true">
                        Next<i class="w-icon-long-arrow-right"></i>
                    </a>
                @endif
            </li>
        </ul>
    @endif
</div>
