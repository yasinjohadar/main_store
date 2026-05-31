<?php

namespace App\Services;

use App\Models\HomeBrand;
use App\Models\HomeFeature;
use App\Models\HomeProductBlock;
use App\Models\HomePromoBanner;
use App\Models\HomeSection;
use App\Models\HomeSlide;
use Illuminate\Support\Collection;

class HomePageService
{
    public const UPLOAD_PATH = 'home-page';

    public static function registry(): array
    {
        return [
            'hero_slider' => [
                'label' => 'سلاider الرئيسية',
                'type' => 'slides',
                'partial' => 'hero-slider',
                'container' => null,
            ],
            'icon_boxes' => [
                'label' => 'شريط الميزات',
                'type' => 'items',
                'partial' => 'icon-boxes',
                'container' => 'container',
            ],
            'category_banners' => [
                'label' => 'بانرات ترويجية',
                'type' => 'items',
                'partial' => 'category-banners',
                'container' => 'container',
            ],
            'deals' => [
                'label' => 'عروض اليوم',
                'type' => 'simple',
                'partial' => 'deals-section',
                'container' => 'container',
            ],
            'top_categories' => [
                'label' => 'أهم التصنيفات',
                'type' => 'simple',
                'partial' => 'top-categories',
                'container' => null,
            ],
            'popular_departments' => [
                'label' => 'الأقسام الشائعة',
                'type' => 'simple',
                'partial' => 'popular-departments',
                'container' => 'container',
            ],
            'product_block_1' => [
                'label' => 'قسم منتجات 1',
                'type' => 'product_block',
                'partial' => 'product-wrapper-1',
                'container' => 'container',
                'block_key' => HomeProductBlock::KEY_PRODUCT_1,
            ],
            'product_block_2' => [
                'label' => 'قسم منتجات 2',
                'type' => 'product_block',
                'partial' => 'product-wrapper-2',
                'container' => 'container',
                'block_key' => HomeProductBlock::KEY_PRODUCT_2,
            ],
            'fashion_banner' => [
                'label' => 'بانر أزياء',
                'type' => 'product_block',
                'partial' => 'fashion-banner',
                'container' => 'container',
                'block_key' => HomeProductBlock::KEY_FASHION_BANNER,
            ],
            'product_block_3' => [
                'label' => 'قسم منتجات 3',
                'type' => 'product_block',
                'partial' => 'product-wrapper-3',
                'container' => 'container',
                'block_key' => HomeProductBlock::KEY_PRODUCT_3,
            ],
            'brands' => [
                'label' => 'الماركات / العملاء',
                'type' => 'items',
                'partial' => 'brands',
                'container' => 'container',
            ],
            'blog' => [
                'label' => 'المدونة',
                'type' => 'simple',
                'partial' => 'blog-section',
                'container' => 'container',
            ],
        ];
    }

    public static function registryEntry(string $key): ?array
    {
        return self::registry()[$key] ?? null;
    }

    public function editRouteName(HomeSection $section): ?string
    {
        return match (self::registryEntry($section->key)['type'] ?? null) {
            'slides' => 'admin.home-slides.index',
            'items' => match ($section->key) {
                'icon_boxes' => 'admin.homepage.features.index',
                'category_banners' => 'admin.homepage.promo-banners.index',
                'brands' => 'admin.homepage.brands.index',
                default => null,
            },
            'product_block' => 'admin.homepage.product-blocks.edit',
            'simple' => 'admin.homepage.sections.edit',
            default => 'admin.homepage.sections.edit',
        };
    }

    public function editRouteParams(HomeSection $section): array
    {
        $entry = self::registryEntry($section->key);

        if ($section->key === 'hero_slider') {
            return [];
        }

        if (($entry['type'] ?? null) === 'product_block') {
            return ['blockKey' => $entry['block_key']];
        }

        return ['homeSection' => $section->key];
    }

    public function visibleSections(): Collection
    {
        return HomeSection::query()->visible()->ordered()->get();
    }

    public function allSections(): Collection
    {
        return HomeSection::query()->ordered()->get();
    }

    public function section(string $key): ?HomeSection
    {
        return HomeSection::query()->where('key', $key)->first();
    }

    public function compose(): array
    {
        $sections = $this->visibleSections();
        $productBlocks = HomeProductBlock::query()
            ->with('category')
            ->get()
            ->keyBy('block_key');

        return [
            'homeSections' => $sections,
            'homeFeatures' => HomeFeature::query()->active()->ordered()->get(),
            'homePromoBanners' => HomePromoBanner::query()->active()->ordered()->get(),
            'homeBrands' => HomeBrand::query()->active()->ordered()->get(),
            'homeProductBlocks' => $productBlocks,
            'heroSlides' => HomeSlide::query()->active()->ordered()->get(),
        ];
    }

    public function sectionByKey(Collection $sections, string $key): ?HomeSection
    {
        return $sections->firstWhere('key', $key);
    }

    public function defaultSectionData(string $key): array
    {
        $defaults = [
            'hero_slider' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'icon_boxes' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'category_banners' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'deals' => [
                'title' => 'عروض اليوم الساخنة',
                'subtitle' => null,
                'eyebrow' => 'عروض محدودة',
                'settings' => [
                    'bestsellers_eyebrow' => 'الأكثر طلباً',
                    'bestsellers_title' => 'الأكثر مبيعاً',
                ],
            ],
            'top_categories' => [
                'title' => 'أهم التصنيفات هذا الشهر',
                'subtitle' => 'اكتشف مجموعات مختارة من أفضل التصنيفات',
                'eyebrow' => null,
                'settings' => [],
            ],
            'popular_departments' => [
                'title' => 'الأقسام الشائعة',
                'subtitle' => 'اختر ما يناسبك من مجموعات منتقاة بعناية',
                'eyebrow' => null,
                'settings' => [
                    'tabs' => [
                        ['key' => 'new_arrivals', 'label' => 'وصل حديثاً', 'icon' => 'w-icon-star', 'active' => true],
                        ['key' => 'best_seller', 'label' => 'الأكثر مبيعاً', 'icon' => 'w-icon-sale', 'active' => false],
                        ['key' => 'top_rated', 'label' => 'الأعلى تقييماً', 'icon' => 'w-icon-star-full', 'active' => false],
                        ['key' => 'featured', 'label' => 'مميز', 'icon' => 'w-icon-heart-full', 'active' => false],
                    ],
                ],
            ],
            'product_block_1' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'product_block_2' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'fashion_banner' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'product_block_3' => ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []],
            'brands' => [
                'title' => 'عملاؤنا',
                'subtitle' => null,
                'eyebrow' => 'شركاء النجاح',
                'settings' => [],
            ],
            'blog' => [
                'title' => 'من مدونتنا',
                'subtitle' => null,
                'eyebrow' => 'المدونة',
                'settings' => [
                    'view_all_label' => 'عرض جميع المقالات',
                ],
            ],
        ];

        return $defaults[$key] ?? ['title' => null, 'subtitle' => null, 'eyebrow' => null, 'settings' => []];
    }
}
