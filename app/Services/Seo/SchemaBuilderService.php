<?php

namespace App\Services\Seo;

use App\Models\Product;
use App\Services\SiteSettingsService;

class SchemaBuilderService
{
    public function __construct(protected SiteSettingsService $settings) {}

    public function organization(): array
    {
        $siteUrl = url('/');
        $name = $this->settings->get(SiteSettingsService::KEY_SITE_NAME, config('app.name'));
        $logo = $this->settings->get(SiteSettingsService::KEY_SITE_LOGO);
        $logoUrl = $logo ? \Storage::url($logo) : asset('frontend/assets/images/logo.png');

        $sameAs = array_values(array_filter([
            $this->settings->get(SiteSettingsService::KEY_SITE_FACEBOOK_URL),
            $this->settings->get(SiteSettingsService::KEY_SITE_TWITTER_URL),
            $this->settings->get(SiteSettingsService::KEY_SITE_INSTAGRAM_URL),
            $this->settings->get(SiteSettingsService::KEY_ORGANIZATION_SAMEAS_LINKEDIN),
        ]));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => $siteUrl . '#organization',
            'name' => $name,
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ],
            'description' => $this->settings->get(SiteSettingsService::KEY_SITE_DESCRIPTION),
            'sameAs' => $sameAs ?: null,
            'contactPoint' => $this->settings->get(SiteSettingsService::KEY_SITE_CONTACT_PHONE) ? [
                '@type' => 'ContactPoint',
                'telephone' => $this->settings->get(SiteSettingsService::KEY_SITE_CONTACT_PHONE),
                'contactType' => 'customer service',
                'email' => $this->settings->get(SiteSettingsService::KEY_SITE_CONTACT_EMAIL),
            ] : null,
        ]);
    }

    public function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/') . '#website',
            'name' => $this->settings->get(SiteSettingsService::KEY_SITE_NAME, config('app.name')),
            'url' => url('/'),
            'publisher' => ['@id' => url('/') . '#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('frontend.shop.index') . '?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public function product(Product $product): array
    {
        $url = route('frontend.product.show', $product->slug);
        $image = $product->images->first()?->path;
        $imageUrl = $image ? (\Storage::url($image)) : null;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => strip_tags($product->short_description ?? $product->description ?? ''),
            'sku' => $product->sku,
            'url' => $url,
            'image' => $imageUrl,
            'offers' => [
                '@type' => 'Offer',
                'url' => $url,
                'priceCurrency' => 'SAR',
                'price' => (string) $product->price,
                'availability' => $product->stock_quantity > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];

        if ($product->brand) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand->name,
            ];
        }

        if ($product->reviews_avg_rating) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round((float) $product->reviews_avg_rating, 1),
                'reviewCount' => $product->reviews()->where('status', 'approved')->count(),
            ];
        }

        return $schema;
    }

    public function breadcrumbs(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(function ($item, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            })->all(),
        ];
    }

    public function webPage(string $name, string $url, ?string $description = null, string $type = 'WebPage'): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $name,
            'url' => $url,
            'description' => $description,
            'isPartOf' => ['@id' => url('/') . '#website'],
        ]);
    }

    public function faqPage(array $questions): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($questions)->map(fn ($q) => [
                '@type' => 'Question',
                'name' => $q['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $q['answer'],
                ],
            ])->all(),
        ];
    }
}
