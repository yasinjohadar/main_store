<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'event',
        'locale',
        'name',
        'subject',
        'body_html',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function events(): array
    {
        return [
            'order_created' => 'إنشاء طلب جديد',
            'order_confirmation' => 'تأكيد الطلب للعميل',
            'new_order_admin' => 'طلب جديد للإدارة',
            'order_status_changed' => 'تغيير حالة الطلب',
            'low_stock_alert' => 'تنبيه مخزون منخفض',
            'back_in_stock' => 'عودة المنتج للمخزون',
            'order_return_status' => 'تحديث حالة طلب المرتجع',
            'user_registered' => 'تسجيل مستخدم جديد',
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'abandoned_cart' => 'تذكير بسلة مهجورة',
            'review_request' => 'طلب مراجعة بعد الشراء',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEventAndLocale($query, string $event, string $locale)
    {
        return $query->where('event', $event)
            ->where('locale', $locale)
            ->active();
    }
}

