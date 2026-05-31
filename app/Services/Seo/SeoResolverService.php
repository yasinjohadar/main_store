<?php

namespace App\Services\Seo;

use App\DTOs\SeoData;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Services\SiteSettingsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoResolverService
{
    public function __construct(
        protected SiteSettingsService $settings,
        protected SchemaBuilderService $schemaBuilder
    ) {}

    public function forSite(?string $title = null, ?string $description = null): SeoData
    {
        $siteName = $this->settings->get(SiteSettingsService::KEY_SITE_NAME, config('app.name'));

        return new SeoData(
            title: $title ?: $siteName,
            description: $description ?: $this->settings->get(SiteSettingsService::KEY_SITE_META_DESCRIPTION),
            keywords: $this->settings->get(SiteSettingsService::KEY_SITE_META_KEYWORDS),
            canonical: url('/'),
            ogImage: $this->logoUrl(),
            schemas: [
                $this->schemaBuilder->organization(),
                $this->schemaBuilder->website(),
            ],
            h1: $siteName,
        );
    }

    public function resolve(Model $model, ?string $fallbackCanonical = null): SeoData
    {
        return match (true) {
            $model instanceof Product => $this->forProduct($model),
            $model instanceof Category => $this->forCategory($model),
            $model instanceof BlogPost => $this->forBlogPost($model),
            $model instanceof Page => $this->forPage($model),
            default => $this->fromSeoable($model, $fallbackCanonical),
        };
    }

    public function forProduct(Product $product): SeoData
    {
        $canonical = $product->canonical_url ?: route('frontend.product.show', $product->slug);
        $image = $this->resolveImage($product->og_image, $product->images->first()?->path ?? null);

        $seo = new SeoData(
            title: ($product->meta_title ?: $product->name) . ' - ' . config('app.name'),
            description: $product->getSeoDescription(),
            keywords: $product->meta_keywords,
            canonical: $canonical,
            robots: $product->getSeoRobots(),
            ogTitle: $product->og_title ?: $product->name,
            ogDescription: $product->og_description ?: strip_tags($product->short_description ?? ''),
            ogImage: $image,
            ogType: 'product',
            twitterTitle: $product->og_title ?: $product->name,
            twitterDescription: $product->og_description,
            twitterImage: $image,
            focusKeyword: $product->focus_keyword,
            schemas: [
                $this->schemaBuilder->organization(),
                $this->schemaBuilder->website(),
                $this->schemaBuilder->product($product),
                $this->schemaBuilder->breadcrumbs([
                    ['name' => 'الرئيسية', 'url' => route('frontend.home')],
                    ['name' => 'المتجر', 'url' => route('frontend.shop.index')],
                    ...($product->category ? [['name' => $product->category->name, 'url' => route('frontend.category.show', $product->category->slug)]] : []),
                    ['name' => $product->name, 'url' => $canonical],
                ]),
            ],
            h1: $product->name,
        );

        return $seo;
    }

    public function forCategory(Category $category): SeoData
    {
        $canonical = $category->canonical_url ?: route('frontend.category.show', $category->slug);

        return new SeoData(
            title: ($category->meta_title ?: $category->name) . ' - ' . config('app.name'),
            description: $category->getSeoDescription(),
            keywords: $category->meta_keywords,
            canonical: $canonical,
            robots: $category->getSeoRobots(),
            ogTitle: $category->og_title ?: $category->name,
            ogDescription: $category->og_description ?: $category->description,
            ogImage: $this->resolveImage($category->og_image, $category->image ?? null),
            focusKeyword: $category->focus_keyword,
            schemas: [
                $this->schemaBuilder->organization(),
                $this->schemaBuilder->website(),
                $this->schemaBuilder->breadcrumbs([
                    ['name' => 'الرئيسية', 'url' => route('frontend.home')],
                    ['name' => 'المتجر', 'url' => route('frontend.shop.index')],
                    ['name' => $category->name, 'url' => $canonical],
                ]),
            ],
            h1: $category->name,
        );
    }

    public function forBlogPost(BlogPost $post): SeoData
    {
        $meta = $post->getSeoMetaTags();
        $canonical = $meta['canonical'] ?? route('frontend.blog.show', $post->slug);
        $image = $this->resolveImage($post->og_image, $post->featured_image);

        return new SeoData(
            title: ($meta['title'] ?? $post->title) . ' - ' . config('app.name'),
            description: $meta['description'] ?? null,
            keywords: $meta['keywords'] ?? null,
            canonical: $canonical,
            robots: $meta['robots'] ?? 'index,follow',
            ogTitle: $meta['og:title'] ?? $post->title,
            ogDescription: $meta['og:description'] ?? null,
            ogImage: $image,
            ogType: $post->og_type ?? 'article',
            ogLocale: $post->og_locale ?? 'ar_SA',
            twitterCard: $post->twitter_card ?? 'summary_large_image',
            twitterTitle: $meta['twitter:title'] ?? null,
            twitterDescription: $meta['twitter:description'] ?? null,
            twitterImage: $this->resolveImage($post->twitter_image, $post->featured_image),
            focusKeyword: $post->focus_keyword,
            schemas: [
                $this->schemaBuilder->organization(),
                $this->schemaBuilder->website(),
                $post->getSchemaJsonLd(),
                $this->schemaBuilder->breadcrumbs([
                    ['name' => 'الرئيسية', 'url' => route('frontend.home')],
                    ['name' => 'المدونة', 'url' => route('frontend.blog.index')],
                    ['name' => $post->title, 'url' => $canonical],
                ]),
            ],
            h1: $post->title,
        );
    }

    public function forPage(Page $page): SeoData
    {
        $canonical = $page->canonical_url ?: ($page->route_name ? route($page->route_name) : url('/' . $page->slug));

        return new SeoData(
            title: ($page->meta_title ?: $page->title) . ' - ' . config('app.name'),
            description: $page->getSeoDescription(),
            keywords: $page->meta_keywords,
            canonical: $canonical,
            robots: $page->getSeoRobots(),
            ogTitle: $page->og_title ?: $page->title,
            ogDescription: $page->og_description ?: $page->excerpt,
            ogImage: $this->resolveImage($page->og_image, null),
            focusKeyword: $page->focus_keyword,
            schemas: [
                $this->schemaBuilder->organization(),
                $this->schemaBuilder->website(),
                $this->schemaBuilder->webPage($page->title, $canonical, $page->getSeoDescription()),
            ],
            h1: $page->title,
        );
    }

    public function forStaticPage(string $title, string $routeName, ?string $description = null, string $schemaType = 'WebPage'): SeoData
    {
        $page = Page::where('route_name', $routeName)->where('is_active', true)->first();
        if ($page) {
            return $this->forPage($page);
        }

        $canonical = route($routeName);

        return new SeoData(
            title: $title . ' - ' . config('app.name'),
            description: $description,
            canonical: $canonical,
            schemas: [
                $this->schemaBuilder->organization(),
                $this->schemaBuilder->website(),
                $this->schemaBuilder->webPage($title, $canonical, $description, $schemaType),
            ],
            h1: $title,
        );
    }

    protected function fromSeoable(Model $model, ?string $fallbackCanonical): SeoData
    {
        if (! method_exists($model, 'getSeoTitle')) {
            return $this->forSite();
        }

        return new SeoData(
            title: $model->getSeoTitle() . ' - ' . config('app.name'),
            description: $model->getSeoDescription(),
            keywords: $model->getSeoKeywords(),
            canonical: $model->getSeoCanonical() ?: $fallbackCanonical,
            robots: $model->getSeoRobots(),
            ogTitle: $model->getSeoOgTitle(),
            ogDescription: $model->getSeoOgDescription(),
            ogImage: $this->resolveImage($model->getSeoOgImage(), null),
            focusKeyword: $model->getSeoFocusKeyword(),
            schemas: [$this->schemaBuilder->organization(), $this->schemaBuilder->website()],
            h1: $model->getSeoTitle(),
        );
    }

    protected function resolveImage(?string $ogImage, ?string $fallback): ?string
    {
        $path = $ogImage ?: $fallback;
        if (! $path) {
            return $this->logoUrl();
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->exists($path)
            ? Storage::url($path)
            : (function_exists('blog_image_url') ? blog_image_url($path) : asset($path));
    }

    protected function logoUrl(): ?string
    {
        $logo = $this->settings->get(SiteSettingsService::KEY_SITE_LOGO);
        if (! $logo) {
            return asset('frontend/assets/images/logo.png');
        }

        return Storage::disk('public')->exists($logo) ? Storage::url($logo) : asset($logo);
    }
}
