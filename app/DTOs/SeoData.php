<?php

namespace App\DTOs;

class SeoData
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $keywords = null,
        public ?string $canonical = null,
        public string $robots = 'index,follow',
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogImage = null,
        public string $ogType = 'website',
        public string $ogLocale = 'ar_SA',
        public ?string $twitterCard = 'summary_large_image',
        public ?string $twitterTitle = null,
        public ?string $twitterDescription = null,
        public ?string $twitterImage = null,
        public ?string $focusKeyword = null,
        public array $schemas = [],
        public ?string $h1 = null,
    ) {}

    public function ogTitleResolved(): string
    {
        return $this->ogTitle ?: $this->title;
    }

    public function ogDescriptionResolved(): ?string
    {
        return $this->ogDescription ?: $this->description;
    }

    public function twitterTitleResolved(): string
    {
        return $this->twitterTitle ?: $this->ogTitleResolved();
    }

    public function twitterDescriptionResolved(): ?string
    {
        return $this->twitterDescription ?: $this->ogDescriptionResolved();
    }
}
