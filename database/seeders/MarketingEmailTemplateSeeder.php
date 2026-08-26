<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class MarketingEmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'abandoned_cart_ar',
                'event' => 'abandoned_cart',
                'locale' => 'ar',
                'name' => 'تذكير بسلة مهجورة',
                'subject' => 'نسيت شيئاً في سلتك يا {{ customer_name }}؟',
                'body_html' => '<p>مرحباً {{ customer_name }},</p><p>لا تزال المنتجات التالية بانتظارك في سلتك:</p><p>{{ cart_items_list }}</p><p><a href="{{ cart_url }}">أكمل طلبك الآن</a></p>',
                'description' => 'يُرسل عند ترك السلة دون إتمام الشراء لفترة محددة',
            ],
            [
                'key' => 'review_request_ar',
                'event' => 'review_request',
                'locale' => 'ar',
                'name' => 'طلب مراجعة بعد الشراء',
                'subject' => 'كيف كانت تجربتك مع طلبك #{{ order_number }}؟',
                'body_html' => '<p>مرحباً {{ customer_name }},</p><p>نأمل أن تكون راضياً عن طلبك #{{ order_number }}. شاركنا رأيك بتقييم المنتجات التي اشتريتها.</p><p><a href="{{ order_url }}">أضف تقييمك</a></p>',
                'description' => 'يُرسل للعميل بعد اكتمال الطلب لطلب تقييم المنتجات',
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
