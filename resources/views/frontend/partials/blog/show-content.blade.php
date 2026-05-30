@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $imageUrl = $post->featured_image
        ? Storage::url($post->featured_image)
        : asset('frontend/assets/images/blog/single/1.jpg');
    $authorName = $post->author->name ?? 'الفريق';
    $commentCount = $comments->count();
    $shareUrl = urlencode(route('frontend.blog.show', $post->slug));
    $shareTitle = urlencode($post->title);
@endphp

<article class="store-blog-show">
    <div class="store-blog-show__hero">
        <img src="{{ $imageUrl }}" alt="{{ $post->title }}" width="930" height="500" loading="eager">
        <span class="store-blog-show__hero-overlay"></span>
        @if($post->category)
            <a href="{{ route('frontend.blog.category', $post->category->slug) }}" class="store-blog-show__category">
                {{ $post->category->name }}
            </a>
        @endif
    </div>

    <header class="store-blog-show__head">
        <div class="store-blog-show__meta">
            <span class="store-blog-show__meta-item">
                <i class="w-icon-user"></i>
                <span>{{ $authorName }}</span>
            </span>
            <span class="store-blog-show__meta-dot"></span>
            <time class="store-blog-show__meta-item" datetime="{{ $post->published_at?->toDateString() }}">
                <i class="w-icon-calendar"></i>
                <span>{{ $post->published_at?->locale('ar')->translatedFormat('j F Y') }}</span>
            </time>
            <span class="store-blog-show__meta-dot"></span>
            <a href="#store-blog-comments" class="store-blog-show__meta-item store-blog-show__meta-link">
                <i class="w-icon-comments"></i>
                <span>{{ $commentCount }} {{ $commentCount === 1 ? 'تعليق' : 'تعليقات' }}</span>
            </a>
            @if($post->views_count ?? null)
                <span class="store-blog-show__meta-dot d-none d-sm-inline"></span>
                <span class="store-blog-show__meta-item d-none d-sm-inline-flex">
                    <i class="far fa-eye"></i>
                    <span>{{ number_format($post->views_count) }} مشاهدة</span>
                </span>
            @endif
        </div>

        <h1 class="store-blog-show__title">{{ $post->title }}</h1>

        @if($post->excerpt)
            <p class="store-blog-show__excerpt">{{ $post->excerpt }}</p>
        @endif
    </header>

    <div class="store-blog-show__content">
        {!! $post->content !!}
    </div>

    @if($post->tags->isNotEmpty())
        <div class="store-blog-show__tags">
            <span class="store-blog-show__tags-label"><i class="w-icon-tag"></i> الوسوم:</span>
            <div class="store-blog-show__tags-list">
                @foreach($post->tags as $tag)
                    <a href="{{ route('frontend.blog.tag', $tag->slug) }}" class="store-blog-show__tag">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="store-blog-show__share">
        <span class="store-blog-show__share-label">شارك المقال:</span>
        <div class="store-blog-show__share-links">
            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                class="store-blog-show__share-btn" target="_blank" rel="noopener noreferrer" aria-label="مشاركة على X">
                <i class="w-icon-twitter"></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                class="store-blog-show__share-btn" target="_blank" rel="noopener noreferrer" aria-label="مشاركة على فيسبوك">
                <i class="w-icon-facebook"></i>
            </a>
            <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                class="store-blog-show__share-btn" target="_blank" rel="noopener noreferrer" aria-label="مشاركة على واتساب">
                <i class="fab fa-whatsapp"></i>
            </a>
            <button type="button" class="store-blog-show__share-btn store-blog-show__share-copy"
                data-copy-url="{{ route('frontend.blog.show', $post->slug) }}" aria-label="نسخ الرابط" title="نسخ الرابط">
                <i class="fas fa-link"></i>
            </button>
        </div>
    </div>

    @if($relatedPosts->isNotEmpty())
        <section class="store-blog-show__related">
            <div class="store-blog-show__related-head">
                <h2 class="store-blog-show__related-title">مقالات ذات صلة</h2>
                <p class="store-blog-show__related-meta">قد يهمك أيضاً</p>
            </div>
            <div class="row cols-lg-3 cols-md-2 cols-sm-2 cols-1 gutter-sm">
                @foreach($relatedPosts as $relatedPost)
                    @php
                        $relatedImage = $relatedPost->featured_image
                            ? Storage::url($relatedPost->featured_image)
                            : asset('frontend/assets/images/blog/single/2.jpg');
                    @endphp
                    <div class="store-blog-show__related-col">
                        <article class="home-blog-card store-blog-show__related-card">
                            <a href="{{ route('frontend.blog.show', $relatedPost->slug) }}" class="home-blog-card__media">
                                <img src="{{ $relatedImage }}" alt="{{ $relatedPost->title }}" width="296" height="190" loading="lazy">
                                @if($relatedPost->category)
                                    <span class="home-blog-card__cat">{{ $relatedPost->category->name }}</span>
                                @endif
                            </a>
                            <div class="home-blog-card__body">
                                <div class="home-blog-card__meta">
                                    <time datetime="{{ $relatedPost->published_at?->toDateString() }}">
                                        {{ $relatedPost->published_at?->locale('ar')->translatedFormat('j F Y') }}
                                    </time>
                                </div>
                                <h3 class="home-blog-card__title">
                                    <a href="{{ route('frontend.blog.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                                </h3>
                                <a href="{{ route('frontend.blog.show', $relatedPost->slug) }}" class="home-blog-card__link">
                                    <span>اقرأ المزيد</span>
                                    <i class="w-icon-long-arrow-left"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="store-blog-show__comments" id="store-blog-comments">
        <div class="store-blog-show__comments-head">
            <h2 class="store-blog-show__comments-title">
                {{ $commentCount }} {{ $commentCount === 1 ? 'تعليق' : 'تعليقات' }}
            </h2>
        </div>

        @if(session('success'))
            <div class="store-blog-show__alert store-blog-show__alert--success">{{ session('success') }}</div>
        @endif

        <ul class="store-blog-show__comments-list">
            @forelse($comments as $comment)
                <li class="store-blog-show__comment">
                    <div class="store-blog-show__comment-avatar">
                        <span>{{ Str::substr($comment->user->name ?? 'ز', 0, 1) }}</span>
                    </div>
                    <div class="store-blog-show__comment-body">
                        <div class="store-blog-show__comment-top">
                            <strong class="store-blog-show__comment-author">{{ $comment->user->name ?? 'زائر' }}</strong>
                            <time class="store-blog-show__comment-date">
                                {{ $comment->created_at->locale('ar')->translatedFormat('j F Y — h:i a') }}
                            </time>
                        </div>
                        <p class="store-blog-show__comment-text">{{ $comment->content }}</p>
                    </div>
                </li>
            @empty
                <li class="store-blog-show__comments-empty">
                    <i class="w-icon-comments"></i>
                    <p>لا توجد تعليقات بعد. كن أول من يعلق!</p>
                </li>
            @endforelse
        </ul>

        <div class="store-blog-show__reply">
            <h3 class="store-blog-show__reply-title">اترك تعليقاً</h3>
            @auth
                <p class="store-blog-show__reply-hint">سيتم مراجعة تعليقك قبل نشره.</p>
                <form method="POST" action="{{ route('frontend.blog.comment.store', $post->slug) }}" class="store-blog-show__reply-form">
                    @csrf
                    @error('content')
                        <p class="store-blog-show__reply-error">{{ $message }}</p>
                    @enderror
                    <textarea name="content" rows="5" class="form-control store-blog-show__reply-input"
                        placeholder="اكتب تعليقك هنا..." required>{{ old('content') }}</textarea>
                    <button type="submit" class="store-blog-show__reply-btn">
                        <span>إرسال التعليق</span>
                        <i class="w-icon-long-arrow-left"></i>
                    </button>
                </form>
            @else
                <p class="store-blog-show__reply-login">
                    <a href="{{ route('login') }}">سجّل الدخول</a> لإضافة تعليق.
                </p>
            @endauth
        </div>
    </section>
</article>
