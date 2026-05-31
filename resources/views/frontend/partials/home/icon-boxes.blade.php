@if(($homeFeatures ?? collect())->isNotEmpty())
<div class="home-features appear-animate">
    @foreach($homeFeatures as $index => $feature)
        <article class="home-feature-card home-feature-card--{{ $feature->tone }}"
            style="--feature-delay: {{ $index * 0.08 }}s;">
            <span class="home-feature-card__icon-wrap">
                <i class="{{ $feature->icon }}"></i>
            </span>
            <div class="home-feature-card__body">
                <h4 class="home-feature-card__title">{{ $feature->title }}</h4>
                <p class="home-feature-card__desc">{{ $feature->description }}</p>
            </div>
            <span class="home-feature-card__shine" aria-hidden="true"></span>
        </article>
    @endforeach
</div>
@endif
