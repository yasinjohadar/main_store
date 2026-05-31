<?php

namespace App\Services\Seo;

use Illuminate\Support\Str;

class AiSeoService
{
    public function suggestForEntity(string $type, string $title, ?string $description = null, ?string $content = null): array
    {
        $plain = strip_tags($content ?? $description ?? '');
        $contextPath = base_path('project-context.md');
        $toneHint = is_readable($contextPath)
            ? ' — نبرة احترافية ودودة بالعربية الفصحى المبسطة'
            : '';

        return [
            'meta_title' => Str::limit($title, 60),
            'meta_description' => Str::limit(($plain ?: ($description ?? '')) . $toneHint, 160),
            'meta_keywords' => '',
            'focus_keyword' => Str::limit($title, 40),
            'og_title' => Str::limit($title, 60),
            'og_description' => Str::limit($plain, 200),
            'schema_type' => match ($type) {
                'product' => 'Product',
                'category' => 'CollectionPage',
                'blog' => 'Article',
                default => 'WebPage',
            },
        ];
    }
}

