<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'about', 'route_name' => 'frontend.about', 'title' => 'من نحن', 'schema_type' => 'AboutPage'],
            ['slug' => 'contact', 'route_name' => 'frontend.contact', 'title' => 'اتصل بنا', 'schema_type' => 'ContactPage'],
            ['slug' => 'faq', 'route_name' => 'frontend.faq', 'title' => 'الأسئلة الشائعة', 'schema_type' => 'FAQPage'],
            ['slug' => 'privacy', 'route_name' => 'frontend.privacy', 'title' => 'سياسة الخصوصية', 'schema_type' => 'WebPage'],
            ['slug' => 'terms', 'route_name' => 'frontend.terms', 'title' => 'الشروط والأحكام', 'schema_type' => 'WebPage'],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['route_name' => $data['route_name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
