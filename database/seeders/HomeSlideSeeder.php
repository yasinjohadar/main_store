<?php

namespace Database\Seeders;

use App\Models\HomeSlide;
use App\Support\SiteTheme;
use Illuminate\Database\Seeder;

class HomeSlideSeeder extends Seeder
{
    public function run(): void
    {
        if (HomeSlide::query()->exists()) {
            return;
        }

        $shopUrl = route('frontend.shop.index', [], false) ?: '/';

        $slides = [
            [
                'title' => 'للجري والرياضة',
                'subtitle' => 'أحذية رجالية مخصصة',
                'description' => 'تصاميم خفيفة ومريحة لأداء يومي استثنائي',
                'badge' => 'خصم 30%',
                'background_image' => null,
                'product_image' => null,
                'background_color' => '#e8edf3',
                'accent_color' => SiteTheme::DEFAULT_PRIMARY,
                'button_text' => 'تسوق الآن',
                'button_url' => $shopUrl,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'حقائب ظهر عصرية',
                'subtitle' => 'تسلق الجبال',
                'description' => 'العرض ساري حتى نهاية هذا الأسبوع',
                'badge' => 'عرض الأسبوع',
                'background_image' => null,
                'product_image' => null,
                'background_color' => '#eef1f5',
                'accent_color' => '#0f766e',
                'button_text' => 'تسوق الآن',
                'button_url' => $shopUrl,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'أدوات التزلج',
                'subtitle' => 'مجموعة رائجة',
                'description' => 'اختيارات شبابية بأسعار تنافسية',
                'badge' => 'الأكثر مبيعاً',
                'background_image' => null,
                'product_image' => null,
                'background_color' => '#f2f3f5',
                'accent_color' => '#c2410c',
                'button_text' => 'تسوق الآن',
                'button_url' => $shopUrl,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($slides as $index => $slide) {
            $slide['background_image'] = 'frontend/assets/images/demos/demo-rtl/sliders/slide-' . ($index + 1) . '.jpg';
            $productFiles = ['shoes.png', 'men.png', 'skate.png'];
            $slide['product_image'] = 'frontend/assets/images/demos/demo-rtl/sliders/' . $productFiles[$index];
            HomeSlide::create($slide);
        }
    }
}
