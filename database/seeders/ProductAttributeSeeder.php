<?php

namespace Database\Seeders;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Seeder;

class ProductAttributeSeeder extends Seeder
{
    /**
     * سمات منتجات عامة شائعة في المتاجر الإلكترونية.
     */
    public function run(): void
    {
        $attributes = [
            [
                'slug' => 'color',
                'name' => 'اللون',
                'type' => 'color',
                'order' => 1,
                'values' => [
                    ['value' => 'أبيض', 'slug' => 'white', 'color_hex' => '#FFFFFF', 'order' => 1],
                    ['value' => 'أسود', 'slug' => 'black', 'color_hex' => '#212121', 'order' => 2],
                    ['value' => 'رمادي', 'slug' => 'gray', 'color_hex' => '#9E9E9E', 'order' => 3],
                    ['value' => 'أحمر', 'slug' => 'red', 'color_hex' => '#E53935', 'order' => 4],
                    ['value' => 'أزرق', 'slug' => 'blue', 'color_hex' => '#1E88E5', 'order' => 5],
                    ['value' => 'أخضر', 'slug' => 'green', 'color_hex' => '#43A047', 'order' => 6],
                    ['value' => 'أصفر', 'slug' => 'yellow', 'color_hex' => '#FDD835', 'order' => 7],
                    ['value' => 'برتقالي', 'slug' => 'orange', 'color_hex' => '#FB8C00', 'order' => 8],
                    ['value' => 'بنفسجي', 'slug' => 'purple', 'color_hex' => '#8E24AA', 'order' => 9],
                    ['value' => 'وردي', 'slug' => 'pink', 'color_hex' => '#EC407A', 'order' => 10],
                    ['value' => 'بني', 'slug' => 'brown', 'color_hex' => '#795548', 'order' => 11],
                    ['value' => 'بيج', 'slug' => 'beige', 'color_hex' => '#D7CCC8', 'order' => 12],
                    ['value' => 'ذهبي', 'slug' => 'gold', 'color_hex' => '#FFD700', 'order' => 13],
                    ['value' => 'فضي', 'slug' => 'silver', 'color_hex' => '#C0C0C0', 'order' => 14],
                    ['value' => 'كحلي', 'slug' => 'navy', 'color_hex' => '#1A237E', 'order' => 15],
                ],
            ],
            [
                'slug' => 'size',
                'name' => 'المقاس',
                'type' => 'select',
                'order' => 2,
                'values' => [
                    ['value' => 'XS', 'slug' => 'xs', 'order' => 1],
                    ['value' => 'S', 'slug' => 's', 'order' => 2],
                    ['value' => 'M', 'slug' => 'm', 'order' => 3],
                    ['value' => 'L', 'slug' => 'l', 'order' => 4],
                    ['value' => 'XL', 'slug' => 'xl', 'order' => 5],
                    ['value' => 'XXL', 'slug' => 'xxl', 'order' => 6],
                    ['value' => '3XL', 'slug' => '3xl', 'order' => 7],
                    ['value' => 'مقاس موحد', 'slug' => 'one-size', 'order' => 8],
                ],
            ],
            [
                'slug' => 'shoe-size',
                'name' => 'مقاس الحذاء',
                'type' => 'select',
                'order' => 3,
                'values' => array_map(
                    fn (int $n) => ['value' => (string) $n, 'slug' => 'eu-' . $n, 'order' => $n - 35],
                    range(36, 45)
                ),
            ],
            [
                'slug' => 'material',
                'name' => 'الخامة',
                'type' => 'select',
                'order' => 4,
                'values' => [
                    ['value' => 'قطن', 'slug' => 'cotton', 'order' => 1],
                    ['value' => 'بوليستر', 'slug' => 'polyester', 'order' => 2],
                    ['value' => 'جلد', 'slug' => 'leather', 'order' => 3],
                    ['value' => 'دنيم', 'slug' => 'denim', 'order' => 4],
                    ['value' => 'صوف', 'slug' => 'wool', 'order' => 5],
                    ['value' => 'حرير', 'slug' => 'silk', 'order' => 6],
                    ['value' => 'كتان', 'slug' => 'linen', 'order' => 7],
                    ['value' => 'معدن', 'slug' => 'metal', 'order' => 8],
                    ['value' => 'ستانلس ستيل', 'slug' => 'stainless-steel', 'order' => 9],
                    ['value' => 'بلاستيك', 'slug' => 'plastic', 'order' => 10],
                    ['value' => 'خشب', 'slug' => 'wood', 'order' => 11],
                    ['value' => 'زجاج', 'slug' => 'glass', 'order' => 12],
                    ['value' => 'مطاط', 'slug' => 'rubber', 'order' => 13],
                ],
            ],
            [
                'slug' => 'storage',
                'name' => 'سعة التخزين',
                'type' => 'select',
                'order' => 5,
                'values' => [
                    ['value' => '32 GB', 'slug' => '32gb', 'order' => 1],
                    ['value' => '64 GB', 'slug' => '64gb', 'order' => 2],
                    ['value' => '128 GB', 'slug' => '128gb', 'order' => 3],
                    ['value' => '256 GB', 'slug' => '256gb', 'order' => 4],
                    ['value' => '512 GB', 'slug' => '512gb', 'order' => 5],
                    ['value' => '1 TB', 'slug' => '1tb', 'order' => 6],
                    ['value' => '2 TB', 'slug' => '2tb', 'order' => 7],
                ],
            ],
            [
                'slug' => 'ram',
                'name' => 'ذاكرة RAM',
                'type' => 'select',
                'order' => 6,
                'values' => [
                    ['value' => '4 GB', 'slug' => '4gb', 'order' => 1],
                    ['value' => '8 GB', 'slug' => '8gb', 'order' => 2],
                    ['value' => '16 GB', 'slug' => '16gb', 'order' => 3],
                    ['value' => '32 GB', 'slug' => '32gb', 'order' => 4],
                    ['value' => '64 GB', 'slug' => '64gb', 'order' => 5],
                ],
            ],
            [
                'slug' => 'volume',
                'name' => 'الحجم',
                'type' => 'select',
                'order' => 7,
                'values' => [
                    ['value' => '50 مل', 'slug' => '50ml', 'order' => 1],
                    ['value' => '100 مل', 'slug' => '100ml', 'order' => 2],
                    ['value' => '250 مل', 'slug' => '250ml', 'order' => 3],
                    ['value' => '500 مل', 'slug' => '500ml', 'order' => 4],
                    ['value' => '1 لتر', 'slug' => '1l', 'order' => 5],
                    ['value' => '2 لتر', 'slug' => '2l', 'order' => 6],
                ],
            ],
            [
                'slug' => 'style',
                'name' => 'النمط',
                'type' => 'select',
                'order' => 8,
                'values' => [
                    ['value' => 'كلاسيك', 'slug' => 'classic', 'order' => 1],
                    ['value' => 'عصري', 'slug' => 'modern', 'order' => 2],
                    ['value' => 'رياضي', 'slug' => 'sport', 'order' => 3],
                    ['value' => 'كاجوال', 'slug' => 'casual', 'order' => 4],
                    ['value' => 'رسمي', 'slug' => 'formal', 'order' => 5],
                ],
            ],
        ];

        $createdAttributes = 0;
        $createdValues = 0;

        foreach ($attributes as $attributeData) {
            $values = $attributeData['values'];
            unset($attributeData['values']);

            $attribute = ProductAttribute::firstOrCreate(
                ['slug' => $attributeData['slug']],
                [
                    'name' => $attributeData['name'],
                    'type' => $attributeData['type'],
                    'order' => $attributeData['order'],
                    'is_visible' => true,
                ]
            );

            if ($attribute->wasRecentlyCreated) {
                $createdAttributes++;
            }

            foreach ($values as $valueData) {
                $value = ProductAttributeValue::firstOrCreate(
                    [
                        'product_attribute_id' => $attribute->id,
                        'slug' => $valueData['slug'],
                    ],
                    [
                        'value' => $valueData['value'],
                        'color_hex' => $valueData['color_hex'] ?? null,
                        'order' => $valueData['order'] ?? 0,
                    ]
                );

                if ($value->wasRecentlyCreated) {
                    $createdValues++;
                }
            }

            $this->command->info("✓ {$attribute->name} ({$attribute->values()->count()} قيمة)");
        }

        $this->command->info("تم: {$createdAttributes} سمة جديدة، {$createdValues} قيمة جديدة.");
    }
}
