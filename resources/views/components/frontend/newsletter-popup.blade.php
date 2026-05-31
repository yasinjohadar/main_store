<div class="newsletter-popup mfp-hide" style="background-image: url('{{ $config['image_url'] }}');">
    <div class="newsletter-content">
        @if($config['badge_prefix'] || $config['badge_highlight'])
            <h4 class="font-weight-normal ls-25">
                {{ $config['badge_prefix'] }}
                @if($config['badge_highlight'])
                    <span class="text-primary mr-0">{{ $config['badge_highlight'] }}</span>
                @endif
            </h4>
        @endif
        <h2 class="ls-25">{{ $config['title'] }}</h2>
        @if($config['description'])
            <p class="text-light ls-10">{{ $config['description'] }}</p>
        @endif
        <form action="#" method="post" class="input-wrapper input-wrapper-inline input-wrapper-round store-newsletter-popup-form">
            @csrf
            <input type="email" class="form-control email font-size-md" name="email" id="email2"
                placeholder="{{ $config['email_placeholder'] }}" required>
            <button class="btn btn-dark" type="submit">{{ $config['submit_label'] }}</button>
        </form>
        <div class="form-checkbox d-flex align-items-center">
            <input type="checkbox" class="custom-checkbox" id="hide-newsletter-popup" name="hide-newsletter-popup">
            <label for="hide-newsletter-popup" class="font-size-sm text-light">{{ $config['dismiss_label'] }}</label>
        </div>
    </div>
</div>
