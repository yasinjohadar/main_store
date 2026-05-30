@props([
    'icon',
    'title',
    'subtitle' => null,
    'theme' => 'theme-orders',
])

<div class="account-section-header {{ $theme }}">
    <span class="account-section-header__icon">
        <i class="{{ $icon }}"></i>
    </span>
    <div class="account-section-header__content">
        <h4 class="account-section-header__title">{{ $title }}</h4>
        @if($subtitle)
            <p class="account-section-header__subtitle mb-0">{{ $subtitle }}</p>
        @endif
    </div>
</div>
