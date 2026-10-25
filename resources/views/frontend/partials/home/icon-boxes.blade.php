@if(($homeFeatures ?? collect())->isNotEmpty())
<div class="home-features appear-animate" style="--feature-count: {{ min(4, $homeFeatures->count()) }};">
    @foreach($homeFeatures as $index => $feature)
        <article class="home-feature-card" style="--feature-delay: {{ $index * 0.08 }}s;">
            <span class="home-feature-card__icon-wrap">
                <i class="{{ $feature->icon }}"></i>
            </span>
            <div class="home-feature-card__body">
                <h4 class="home-feature-card__title">{{ $feature->title }}</h4>
                <p class="home-feature-card__desc">{{ $feature->description }}</p>
            </div>
        </article>
    @endforeach
</div>
@endif
