# Katamars - القطمارس القبطي 📖

[![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)](https://github.com/inisoliman/wp-katamars)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## 🌟 الوصف

إضافة ووردبريس احترافية شاملة تقدم القراءات اليومية من الكتاب المقدس، السنكسار القبطي، والتقويم القبطي الأرثوذكسي مع واجهات برمجية متقدمة.

## ✨ المميزات الرئيسية

### 📚 القراءات اليومية
- قراءات رفع بخور باكر
- قراءات القداس الإلهي  
- قراءات رفع بخور عشية
- دعم اللغتين العربية والإنجليزية
- مراعاة الأصوام والأعياد

### 📜 السنكسار اليومي
- سير القديسين والشهداء
- الأحداث التاريخية الكنسية
- البحث المتقدم في السنكسار

### 📅 التقويم القبطي
- تحويل تلقائي بين التقويمين
- عرض الأعياد والمناسبات
- مراعاة الأصوام الكنسية

### 🎛️ لوحة تحكم متقدمة
- إدارة شاملة للقراءات
- إحصائيات مفصلة
- استيراد وتصدير البيانات

## 🚀 التثبيت

### الطريقة 1: من خلال ووردبريس
1. حمّل ملف ZIP من [الإصدارات](https://github.com/inisoliman/wp-katamars/releases)
2. ارفعه عبر لوحة تحكم ووردبريس
3. فعّل الإضافة

### الطريقة 2: عبر FTP
1. حمّل المجلد إلى `/wp-content/plugins/`
2. فعّل الإضافة من لوحة التحكم

### الطريقة 3: استخدام سكريبت الإنشاء
1. ضع ملف `create-katamars-plugin.php` في مجلد المستودع
2. شغّل الأمر: `php create-katamars-plugin.php`
3. ستُنشأ جميع الملفات تلقائياً

## 🎯 الاستخدام

### Shortcodes المتاحة:
[katamars_today] - قراءات اليوم
[katamars_calendar] - التقويم القبطي
[katamars_synaxarium] - السنكسار اليومي
[katamars_feasts] - الأعياد القادمة

### REST API:
GET /wp-json/katamars/v1/readings
GET /wp-json/katamars/v1/synaxarium
GET /wp-json/katamars/v1/calendar
GET /wp-json/katamars/v1/feasts



## 🏗️ بنية المشروع

wp-katamars/
├── katamars.php # الملف الرئيسي
├── create-katamars-plugin.php # سكريبت الإنشاء
├── uninstall.php # حذف الإضافة
├── includes/ # الكلاسات الأساسية
├── admin/ # لوحة التحكم
├── public/ # الواجهة العامة
├── assets/ # CSS & JS & Images
├── data/ # بيانات القراءات
└── languages/ # ملفات الترجمة



## 📋 المتطلبات

- WordPress 5.0+
- PHP 7.2+
- MySQL 5.6+

## 🤝 المساهمة

نرحب بمساهماتكم:
1. Fork المشروع
2. أنشئ فرعاً جديداً
3. اعمل التغييرات
4. أرسل Pull Request

## 👨‍💻 المطور

**Ini Soliman**
- GitHub: [@inisoliman](https://github.com/inisoliman)
- المشروع: [wp-katamars](https://github.com/inisoliman/wp-katamars)

## 📄 الترخيص

مرخص تحت GPL v2 أو أحدث - انظر [LICENSE](LICENSE) للتفاصيل.

---
🙏 **للكنيسة القبطية الأرثوذكسية بمحبة**