<?php

namespace App\Services\Seo;

class SeoScoreService
{
    /**
     * @return array{score: int, checks: array<int, array{label: string, passed: bool, hint: string}>}
     */
    public function score(array $data): array
    {
        $checks = [];

        $title = $data['meta_title'] ?? $data['title'] ?? '';
        $titleLen = mb_strlen($title);
        $checks[] = [
            'label' => 'طول العنوان (50–60 حرف)',
            'passed' => $titleLen >= 30 && $titleLen <= 65,
            'hint' => "الحالي: {$titleLen} حرف",
        ];

        $desc = $data['meta_description'] ?? $data['description'] ?? '';
        $descLen = mb_strlen(strip_tags($desc));
        $checks[] = [
            'label' => 'طول الوصف (120–160 حرف)',
            'passed' => $descLen >= 80 && $descLen <= 170,
            'hint' => "الحالي: {$descLen} حرف",
        ];

        $focus = $data['focus_keyword'] ?? '';
        $checks[] = [
            'label' => 'كلمة مفتاحية محددة',
            'passed' => ! empty(trim($focus)),
            'hint' => 'أضف focus keyword',
        ];

        if ($focus && $title) {
            $checks[] = [
                'label' => 'الكلمة المفتاحية في العنوان',
                'passed' => mb_stripos($title, $focus) !== false,
                'hint' => 'ضمّن الكلمة في meta title',
            ];
        }

        $checks[] = [
            'label' => 'صورة OG',
            'passed' => ! empty($data['og_image'] ?? null),
            'hint' => 'أضف og:image للمشاركة',
        ];

        $checks[] = [
            'label' => 'Canonical URL',
            'passed' => ! empty($data['canonical_url'] ?? null),
            'hint' => 'يُولَّد تلقائياً إن تُرك فارغاً',
        ];

        $passed = collect($checks)->where('passed', true)->count();
        $total = count($checks);

        return [
            'score' => $total > 0 ? (int) round(($passed / $total) * 100) : 0,
            'checks' => $checks,
        ];
    }
}
