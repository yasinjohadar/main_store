@extends('frontend.layouts.master')

@section('title', config('app.name') . ' - الرئيسية')

@section('content')
    @php
        $inContainer = false;
        $defaults = app(\App\Services\HomePageService::class);
    @endphp

    @foreach($homeSections as $section)
        @php
            $entry = \App\Services\HomePageService::registryEntry($section->key);
            if (! $entry) {
                continue;
            }

            $needsContainer = ($entry['container'] ?? null) === 'container';
            $partial = 'frontend.partials.home.' . $entry['partial'];
            $sectionDefaults = $defaults->defaultSectionData($section->key);

            if ($section->key === 'icon_boxes' && ($homeFeatures ?? collect())->isEmpty()) {
                continue;
            }
            if ($section->key === 'category_banners' && ($homePromoBanners ?? collect())->isEmpty()) {
                continue;
            }
            if ($section->key === 'brands' && ($homeBrands ?? collect())->isEmpty()) {
                continue;
            }

            $blockKey = $entry['block_key'] ?? null;
            if ($blockKey) {
                $block = ($homeProductBlocks ?? collect())->get($blockKey);
                if (! $block?->is_active) {
                    continue;
                }
            }
        @endphp

        @if($needsContainer && ! $inContainer)
            <div class="container">
            @php $inContainer = true; @endphp
        @elseif(! $needsContainer && $inContainer)
            </div>
            @php $inContainer = false; @endphp
        @endif

        @includeIf($partial, [
            'section' => $section,
            'sectionDefaults' => $sectionDefaults,
        ])
    @endforeach

    @if($inContainer)
        </div>
    @endif
@endsection
