<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class OrderEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'order_confirmation_ar',
                'event' => 'order_confirmation',
                'locale' => 'ar',
                'name' => 'تأكيد الطلب للعميل',
                'subject' => 'تأكيد طلبك #{{ order_number }}',
                'body_html' => '<p>مرحباً {{ customer_name }},</p><p>شكراً لطلبك. رقم الطلب: <strong>{{ order_number }}</strong></p><p>الإجمالي: {{ total }} ر.س</p>',
                'description' => 'يُرسل للعميل عند إنشاء طلب جديد',
            ],
            [
                'key' => 'new_order_admin_ar',
                'event' => 'new_order_admin',
                'locale' => 'ar',
                'name' => 'طلب جديد للإدارة',
                'subject' => 'طلب جديد #{{ order_number }}',
                'body_html' => '<p>تم استلام طلب جديد.</p><p>رقم الطلب: <strong>{{ order_number }}</strong></p><p>الإجمالي: {{ total }} ر.س</p>',
                'description' => 'يُرسل للإدارة عند طلب جديد',
            ],
            [
                'key' => 'order_status_changed_ar',
                'event' => 'order_status_changed',
                'locale' => 'ar',
                'name' => 'تغيير حالة الطلب',
                'subject' => 'تحديث طلب #{{ order_number }} — {{ status }}',
                'body_html' => '<p>تم تحديث حالة طلبك #{{ order_number }} إلى: <strong>{{ status }}</strong></p>',
                'description' => 'يُرسل للعميل عند تغيير حالة الطلب',
            ],
            [
                'key' => 'low_stock_alert_ar',
                'event' => 'low_stock_alert',
                'locale' => 'ar',
                'name' => 'تنبيه مخزون منخفض',
                'subject' => 'تنبيه: {{ products_count }} منتج بمخزون منخفض',
                'body_html' => '<p>المنتجات التالية بمخزون منخفض:</p><p>{{ products_list }}</p>',
                'description' => 'تنبيه إداري للمخزون المنخفض',
            ],
            [
                'key' => 'back_in_stock_ar',
                'event' => 'back_in_stock',
                'locale' => 'ar',
                'name' => 'عودة المنتج للمخزون',
                'subject' => '{{ product_name }} متوفر الآن',
                'body_html' => '<p>المنتج <strong>{{ product_name }}</strong> متوفر الآن.</p><p><a href="{{ product_url }}">عرض المنتج</a></p>',
                'description' => 'يُرسل للمشتركين عند توفر المنتج',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                array_merge($template, ['is_active' => true]),
            );
        }
    }
}
