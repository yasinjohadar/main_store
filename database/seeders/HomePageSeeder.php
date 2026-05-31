<?php

namespace Database\Seeders;

use App\Models\HomeBrand;
use App\Models\HomeFeature;
use App\Models\HomeProductBlock;
use App\Models\HomePromoBanner;
use App\Models\HomeSection;
use App\Services\HomePageService;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        if (HomeSection::query()->exists()) {
            return;
        }

        $service = app(HomePageService::class);
        $shopUrl = route('frontend.shop.index', [], false) ?: '/shop';

        $sort = 1;
        foreach (HomePageService::registry() as $key => $meta) {
            $defaults = $service->defaultSectionData($key);
            HomeSection::create([
                'key' => $key,
                'title' => $defaults['title'],
                'subtitle' => $defaults['subtitle'],
                'eyebrow' => $defaults['eyebrow'],
                'is_visible' => true,
                'sort_order' => $sort++,
                'settings' => $defaults['settings'],
            ]);
        }

        $features = [
            ['icon' => 'w-icon-truck', 'tone' => 'blue', 'title' => 'شحن وإرجاع مجاني', 'description' => 'لجميع الطلبات فوق 99 ر.س'],
            ['icon' => 'w-icon-bag', 'tone' => 'teal', 'title' => 'دفع آمن', 'description' => 'نضمن لك عملية دفع آمنة'],
            ['icon' => 'w-icon-money', 'tone' => 'amber', 'title' => 'ضمان استرداد الأموال', 'description' => 'خلال 30 يوماً'],
            ['icon' => 'w-icon-chat', 'tone' => 'violet', 'title' => 'دعم العملاء', 'description' => 'تواصل معنا على مدار الساعة'],
        ];
        foreach ($features as $i => $feature) {
            HomeFeature::create([...$feature, 'sort_order' => $i + 1, 'is_active' => true]);
        }

        $promos = [
            [
                'image' => 'frontend/assets/images/demos/demo-rtl/banners/banner-1.jpg',
                'background_color' => '#dfe3e0',
                'tone' => 'light',
                'badge' => 'خصم حتى 20%',
                'title' => 'ملابس رياضية',
                'subtitle' => 'مجموعة جديدة',
                'price_label' => 'يبدأ من',
                'price_text' => '170 ر.س',
                'button_url' => $shopUrl,
            ],
            [
                'image' => 'frontend/assets/images/demos/demo-rtl/banners/banner-2.jpg',
                'background_color' => '#3d3d3d',
                'tone' => 'dark',
                'badge' => 'وصل حديثاً',
                'title' => 'إكسسوارات',
                'subtitle' => 'مجموعة مميزة',
                'price_label' => 'ابتداءً من',
                'price_text' => '90 ر.س',
                'button_url' => $shopUrl,
            ],
        ];
        foreach ($promos as $i => $promo) {
            HomePromoBanner::create([...$promo, 'sort_order' => $i + 1, 'is_active' => true]);
        }

        $blocks = [
            [
                'block_key' => HomeProductBlock::KEY_PRODUCT_1,
                'section_title' => 'الملابس والأزياء',
                'banner_image' => 'frontend/assets/images/demos/demo-rtl/banners/banner-5.jpg',
                'banner_bg_color' => '#ebeced',
                'banner_subtitle' => 'عروض نهاية الأسبوع',
                'banner_title' => 'وصل حديثاً<br><span class="font-weight-normal text-capitalize">مجموعة جديدة</span>',
                'wrapper_class' => 'product-wrapper-1 appear-animate mb-5',
            ],
            [
                'block_key' => HomeProductBlock::KEY_PRODUCT_2,
                'section_title' => 'الإلكترونيات',
                'banner_image' => 'frontend/assets/images/demos/demo-rtl/banners/banner-6.jpg',
                'banner_bg_color' => '#252525',
                'banner_subtitle' => 'مجموعة جديدة',
                'banner_title' => 'أفضل الكاميرات<br><span class="font-weight-normal text-capitalize">بدون مرآة</span>',
                'wrapper_class' => 'product-wrapper-1 appear-animate mb-8',
            ],
            [
                'block_key' => HomeProductBlock::KEY_FASHION_BANNER,
                'banner_image' => 'frontend/assets/images/demos/demo-rtl/banners/banner-7.jpg',
                'banner_bg_color' => '#383839',
                'banner_subtitle' => null,
                'banner_title' => 'أزياء اليوم',
                'settings' => [
                    'discount_percent' => '25',
                    'promo_code' => 'SAVE25',
                    'promo_text' => 'استخدم الكود {code} للحصول على أفضل عرض.',
                ],
            ],
            [
                'block_key' => HomeProductBlock::KEY_PRODUCT_3,
                'section_title' => 'المنزل والمطبخ',
                'banner_image' => 'frontend/assets/images/demos/demo-rtl/banners/banner-8.jpg',
                'banner_bg_color' => '#EAEFF3',
                'banner_subtitle' => 'عروض وتخفيضات',
                'banner_title' => 'الأكثر رواجاً<br><span class="font-weight-normal text-capitalize">أدوات منزلية</span>',
                'wrapper_class' => 'product-wrapper-1 product-wrapper-home appear-animate mb-7',
            ],
        ];
        foreach ($blocks as $i => $block) {
            HomeProductBlock::create([...$block, 'sort_order' => $i + 1, 'is_active' => true, 'button_url' => $shopUrl]);
        }

        foreach (range(1, 12) as $i) {
            HomeBrand::create([
                'name' => 'Brand ' . $i,
                'logo' => 'frontend/assets/images/demos/demo1/brands/' . $i . '.png',
                'url' => $shopUrl,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
