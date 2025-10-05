# القطمارس - Katamars WordPress Plugin

## الوصف

إضافة WordPress للقطمارس - كتاب القراءات الكنسية اليومية والطقسية للكنيسة القبطية الأرثوذكسية.

## المميزات

### القراءات اليومية
- قراءات صلاة الغروب (المزمور والإنجيل)
- قراءات صلاة باكر (المزمور والإنجيل)
- قراءات القداس الإلهي (البولس، الكاثوليكون، الإبركسيس، المزمور، الإنجيل)
- السنكسار اليومي

### الأعياد والمناسبات
- الأعياد السيدية الكبرى
- أعياد السيدة العذراء مريم
- تذكارات القديسين والشهداء
- المواسم الكنسية (الصوم الكبير، الخماسين المقدسة، إلخ)

### التقويم القبطي
- عرض التاريخ القبطي والميلادي
- تحديد المواسم الكنسية
- ربط القراءات بالتواريخ

## التثبيت

1. قم بتحميل ملفات الإضافة
2. ارفع المجلد `wp-katamars` إلى مجلد `/wp-content/plugins/`
3. فعل الإضافة من لوحة تحكم WordPress

## الاستخدام

### عرض القراءات اليومية
```php
// عرض قراءات اليوم
echo do_shortcode('[katamars_daily_readings]');

// عرض قراءات تاريخ معين
echo do_shortcode('[katamars_readings date="2025-01-07"]');
```

### الـ Shortcodes المتاحة

- `[katamars_daily_readings]` - عرض قراءات اليوم الحالي
- `[katamars_readings date="YYYY-MM-DD"]` - عرض قراءات تاريخ محدد
- `[katamars_coptic_calendar]` - عرض التقويم القبطي
- `[katamars_synaxarium]` - عرض السنكسار اليومي
- `[katamars_feasts]` - عرض الأعياد والمناسبات

## الملفات والمجلدات

```
wp-katamars/
├── wp-katamars.php          # الملف الرئيسي للإضافة
├── README.md                # ملف الوصف
├── includes/                # ملفات PHP الأساسية
│   ├── class-katamars-db.php      # كلاس قاعدة البيانات
│   ├── class-katamars-frontend.php # كلاس الواجهة الأمامية
│   ├── class-katamars-admin.php    # كلاس لوحة التحكم
│   └── katamars-functions.php      # الدوال المساعدة
├── assets/                  # ملفات CSS و JavaScript
│   ├── css/
│   └── js/
├── templates/               # قوالب العرض
├── data/                   # ملفات البيانات
└── languages/              # ملفات الترجمة
```

## قاعدة البيانات

### جدول katamars_readings
- `id` - المعرف الفريد
- `coptic_date` - التاريخ القبطي
- `gregorian_date` - التاريخ الميلادي
- `vespers_psalm` - مزمور الغروب
- `vespers_gospel` - إنجيل الغروب
- `matins_psalm` - مزمور باكر
- `matins_gospel` - إنجيل باكر
- `liturgy_pauline` - البولس
- `liturgy_catholic` - الكاثوليكون
- `liturgy_acts` - الإبركسيس
- `liturgy_psalm` - مزمور القداس
- `liturgy_gospel` - إنجيل القداس
- `synaxarium` - السنكسار
- `feast_name` - اسم العيد/المناسبة
- `feast_type` - نوع العيد

### جدول katamars_feasts
- `id` - المعرف الفريد
- `name` - اسم العيد
- `coptic_date` - التاريخ القبطي
- `gregorian_date` - التاريخ الميلادي
- `feast_type` - نوع العيد
- `description` - وصف العيد
- `special_readings` - قراءات خاصة

## التطوير

### المتطلبات
- WordPress 5.0 أو أحدث
- PHP 7.4 أو أحدث
- MySQL 5.7 أو أحدث

### المساهمة
نرحب بالمساهمات! يرجى:
1. عمل Fork للمستودع
2. إنشاء فرع جديد للميزة
3. عمل Commit للتغييرات
4. إرسال Pull Request

## الترخيص

هذا المشروع مرخص تحت رخصة GPL v2 أو أحدث.

## الدعم

لطلب الدعم أو الإبلاغ عن مشاكل، يرجى فتح Issue جديد في GitHub.

## المراجع

- الكتاب المقدس
- القطمارس السنوي للكنيسة القبطية الأرثوذكسية
- قطمارس الصوم الكبير
- السنكسار القبطي

---

**تطوير:** Ibrahim Soliman  
**الموقع:** https://github.com/inisoliman/wp-katamars
