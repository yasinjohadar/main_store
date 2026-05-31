@php
    $reviewCount = $reviews->count();
    $avgRating = $reviewCount > 0 ? round($reviews->avg('rating'), 1) : 0;
    $ratingPercent = $reviewCount > 0 ? ($avgRating / 5) * 100 : 0;
@endphp

<div class="tab-pane" id="product-tab-reviews">
    <div class="row mb-4 store-product-reviews">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="ratings-wrapper store-product-reviews__summary">
                <div class="avg-rating-container">
                    <h4 class="avg-mark font-weight-bolder ls-50">{{ number_format($avgRating, 1) }}</h4>
                    <div class="avg-rating">
                        <p class="text-dark mb-1">متوسط التقييم</p>
                        <div class="ratings-container">
                            <div class="ratings-full">
                                <span class="ratings" style="width: {{ $ratingPercent }}%;"></span>
                                <span class="tooltiptext tooltip-top"></span>
                            </div>
                            <a href="#" class="rating-reviews">({{ $reviewCount }} {{ $reviewCount === 1 ? 'تقييم' : 'تقييمات' }})</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="review-form-wrapper">
                <h3 class="title tab-pane-title font-weight-bold mb-1">أضف تقييمك</h3>
                <p class="mb-3">لن يُنشر بريدك الإلكتروني. الحقول المطلوبة مُعلّمة بـ *</p>
                @auth
                    <form action="{{ route('frontend.product.reviews.store', $product->slug) }}" method="POST" class="review-form">
                        @csrf
                        <div class="rating-form mb-3">
                            <label for="rating">تقييمك لهذا المنتج: *</label>
                            <select name="rating" id="rating" class="form-select" style="max-width:120px" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} نجوم</option>
                                @endfor
                            </select>
                        </div>
                        <input type="text" name="title" class="form-control mb-2" placeholder="عنوان التقييم (اختياري)">
                        <textarea cols="30" rows="6" placeholder="اكتب تقييمك هنا..." class="form-control" name="content" required></textarea>
                        <button type="submit" class="btn btn-dark btn-rounded mt-3">إرسال التقييم</button>
                    </form>
                @else
                    <p class="mb-0">
                        <a href="{{ route('login') }}">سجّل الدخول</a> لكتابة تقييم.
                    </p>
                @endauth
            </div>
        </div>
    </div>

    <div class="tab tab-nav-boxed tab-nav-solid product-card-type tab-nav-outline">
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#show-all">جميع التقييمات ({{ $reviewCount }})</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="show-all">
                <ul class="comments list-style-none">
                    @forelse($reviews as $review)
                        <li class="comment">
                            <div class="comment-body">
                                <figure class="comment-avatar">
                                    <img src="{{ asset('frontend/assets/images/products/default/reviewer-1.jpg') }}"
                                        alt="مُقيّم" width="90" height="90" />
                                </figure>
                                <div class="comment-content">
                                    <h4 class="comment-author">
                                        <a href="#">{{ $review->user->name ?? 'عميل' }}</a>
                                        <span class="comment-date">{{ $review->created_at?->translatedFormat('d M Y') }}</span>
                                    </h4>
                                    <div class="ratings-container comment-rating">
                                        <div class="ratings-full">
                                            <span class="ratings" style="width: {{ ($review->rating / 5) * 100 }}%;"></span>
                                            <span class="tooltiptext tooltip-top"></span>
                                        </div>
                                    </div>
                                    @if($review->title)
                                        <h5 class="comment-title">{{ $review->title }}</h5>
                                    @endif
                                    <p>{{ $review->comment }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="comment">
                            <p class="mb-0 text-muted">لا توجد تقييمات بعد. كن أول من يقيّم هذا المنتج.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
