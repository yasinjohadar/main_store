# إعداد الدفع عبر Stripe

## متغيرات البيئة (اختياري)

يمكن تخزين المفاتيح في لوحة الإدارة → إعدادات Stripe، أو في `.env`:

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

بعد التعديل نفّذ `php artisan config:clear` إن لزم.

## Webhook

- **الرابط:** `{APP_URL}/api/webhooks/stripe`
- **الأحداث المطلوبة:**
  - `checkout.session.completed`
  - `checkout.session.expired`
  - `payment_intent.payment_failed`
  - `charge.refunded`

## وسيلة الدفع

1. احفظ المفاتيح من **إعدادات Stripe** في لوحة الإدارة.
2. تأكد من تفعيل وسيلة «بطاقة ائتمان (Stripe)» في **وسائل الدفع**.
3. للتجربة المحلية استخدم [Stripe CLI](https://stripe.com/docs/stripe-cli) لإعادة توجيه الأحداث.

## بطاقات الاختبار

| السيناريو | الرقم |
|-----------|--------|
| نجاح | `4242 4242 4242 4242` |
| رفض | `4000 0000 0000 0002` |

أي تاريخ مستقبلي، أي CVC من 3 أرقام.

## العملة

المتجر يستخدم **SAR** في Checkout Session عند ضبط عملة المتجر accordingly — تأكد من تفعيل SAR في حساب Stripe.

## التحقق عند العودة

بعد الدفع يعيد Stripe المستخدم إلى صفحة نجاح الطلب مع `?session_id=...`؛ التطبيق يحدّث حالة الدفع فوراً بالإضافة إلى Webhook.
