# أرشيف كود ميت — 2026-07-29

نُقلت هذه الملفات من الشجرة الفعّالة للتطبيق ضمن المرحلة 0 من خطة "تجهيز main_store لمنافسة عالمية"
(راجع `docs/`). لم تُحذف نهائياً حفاظاً على القابلية للرجوع، لكنها غير مُحمّلة عبر autoload
(خارج `app/`) وغير مستخدمة من أي مسار حي.

## المحتويات وسبب الأرشفة

- **`Http/Controllers/Store/`** + **`views/store/`** — مسار متجر بديل قديم (`/store/*`).
  `routes/web.php` يُعيد توجيه كل مسارات `/store/*` بـ 301 إلى `frontend.*` الموحّدة، ولا شيء
  يستدعي هذه الـ controllers أو الـ views بعد الآن.
- **`Models/Customer.php`** — نموذج عميل منفصل عن `User`، لا يوجد له جدول `customers` في
  قاعدة البيانات إطلاقاً (لا توجد migration لإنشائه)، ولم يكن مستخدَماً إلا من
  `Store/CustomerController.php` الميت أعلاه.
- **`Models/Certificate.php`**, **`Models/CertificateTemplate.php`**,
  **`Services/Certificate/CertificateGeneratorService.php`** — بقايا من منصة الدورات (LMS)
  التي تحوّل منها المشروع. الجداول `certificates` و`certificate_templates` حُذفت فعلياً في
  `database/migrations/2026_02_24_190000_drop_course_and_zoom_tables.php`، ونماذج
  `Course`/`CourseEnrollment` التي تعتمد عليها هذه الملفات لم تعد موجودة في المشروع أصلاً —
  أي أن هذا الكود كان سيُسبّب خطأ Class Not Found لو استُدعي.

## الحذف النهائي

يمكن حذف هذا المجلد بأمان لاحقاً بعد فترة مراجعة (تاريخ Git يحتفظ بالمحتوى الكامل على أي حال).
