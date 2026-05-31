<?php

namespace App\Support;

use Illuminate\Support\Collection;

class PermissionGrouper
{
    protected array $groupLabels = [
        'role' => ['label' => 'الأدوار والصلاحيات', 'icon' => 'la-shield-alt'],
        'user' => ['label' => 'المستخدمون', 'icon' => 'la-users'],
        'employee' => ['label' => 'الموظفون', 'icon' => 'la-id-badge'],
        'department' => ['label' => 'الأقسام', 'icon' => 'la-sitemap'],
        'position' => ['label' => 'المناصب', 'icon' => 'la-briefcase'],
        'branch' => ['label' => 'الفروع', 'icon' => 'la-code-branch'],
        'country' => ['label' => 'الدول', 'icon' => 'la-globe'],
        'currency' => ['label' => 'العملات', 'icon' => 'la-coins'],
        'category' => ['label' => 'التصنيفات', 'icon' => 'la-folder'],
        'course' => ['label' => 'الكورسات', 'icon' => 'la-graduation-cap'],
        'product' => ['label' => 'المنتجات', 'icon' => 'la-box'],
        'order' => ['label' => 'الطلبات', 'icon' => 'la-shopping-bag'],
        'review' => ['label' => 'آراء العملاء', 'icon' => 'la-comment-dots'],
        'coupon' => ['label' => 'الكوبونات', 'icon' => 'la-ticket-alt'],
        'dashboard' => ['label' => 'لوحة القيادة', 'icon' => 'la-tachometer-alt'],
        'settings' => ['label' => 'الإعدادات', 'icon' => 'la-cog'],
        'reports' => ['label' => 'التقارير', 'icon' => 'la-chart-bar'],
    ];

    protected array $actionLabels = [
        'list' => 'عرض القائمة',
        'create' => 'إنشاء',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'show' => 'عرض التفاصيل',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'reply' => 'رد',
        'statistics' => 'إحصائيات',
        'change-password' => 'تغيير كلمة المرور',
        'toggle-status' => 'تفعيل / إيقاف',
        'manage' => 'إدارة',
        'view' => 'عرض',
    ];

    public function group(Collection $permissions): array
    {
        $groups = [];

        foreach ($permissions as $permission) {
            $name = $permission->name;
            $parts = explode('-', $name, 2);
            $groupKey = $parts[0] ?? 'other';
            $actionKey = $parts[1] ?? $name;

            if (! isset($groups[$groupKey])) {
                $meta = $this->groupLabels[$groupKey] ?? [
                    'label' => ucfirst(str_replace('-', ' ', $groupKey)),
                    'icon' => 'la-key',
                ];
                $groups[$groupKey] = [
                    'key' => $groupKey,
                    'label' => $meta['label'],
                    'icon' => $meta['icon'],
                    'permissions' => [],
                ];
            }

            $groups[$groupKey]['permissions'][] = [
                'id' => $permission->id,
                'name' => $name,
                'action' => $actionKey,
                'label' => $this->actionLabels[$actionKey] ?? str_replace('-', ' ', $actionKey),
            ];
        }

        foreach ($groups as &$group) {
            usort($group['permissions'], fn ($a, $b) => strcmp($a['label'], $b['label']));
        }
        unset($group);

        uasort($groups, fn ($a, $b) => strcmp($a['label'], $b['label']));

        return array_values($groups);
    }
}
