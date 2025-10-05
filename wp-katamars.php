<?php
/**
 * Plugin Name: القطمارس - Katamars
 * Plugin URI: https://github.com/inisoliman/wp-katamars
 * Description: إضافة WordPress للقطمارس - القراءات الكنسية اليومية والطقسية
 * Version: 1.0.0
 * Author: Ibrahim Soliman
 * License: GPL v2 or later
 * Text Domain: wp-katamars
 * Domain Path: /languages
 */

// منع الوصول المباشر
if (!defined('ABSPATH')) {
    exit;
}

// تعريف الثوابت
define('WP_KATAMARS_VERSION', '1.0.0');
define('WP_KATAMARS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_KATAMARS_PLUGIN_PATH', plugin_dir_path(__FILE__));

// تفعيل الإضافة
register_activation_hook(__FILE__, 'wp_katamars_activate');
register_deactivation_hook(__FILE__, 'wp_katamars_deactivate');

// دالة التفعيل
function wp_katamars_activate() {
    // إنشاء جداول قاعدة البيانات
    wp_katamars_create_tables();
    
    // إضافة البيانات الأولية
    wp_katamars_insert_initial_data();
}

// دالة إلغاء التفعيل
function wp_katamars_deactivate() {
    // تنظيف مؤقت إذا لزم الأمر
}

// تحميل ملفات الإضافة
require_once WP_KATAMARS_PLUGIN_PATH . 'includes/class-katamars-db.php';
require_once WP_KATAMARS_PLUGIN_PATH . 'includes/class-katamars-frontend.php';
require_once WP_KATAMARS_PLUGIN_PATH . 'includes/class-katamars-admin.php';
require_once WP_KATAMARS_PLUGIN_PATH . 'includes/katamars-functions.php';

// تهيئة الإضافة
function wp_katamars_init() {
    // تحميل ملفات الترجمة
    load_plugin_textdomain('wp-katamars', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // تهيئة الكلاسات
    new Katamars_Frontend();
    
    if (is_admin()) {
        new Katamars_Admin();
    }
}
add_action('plugins_loaded', 'wp_katamars_init');

// إنشاء جداول قاعدة البيانات
function wp_katamars_create_tables() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // جدول القراءات اليومية
    $table_readings = $wpdb->prefix . 'katamars_readings';
    $sql_readings = "CREATE TABLE $table_readings (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        coptic_date varchar(50) NOT NULL,
        gregorian_date date NOT NULL,
        vespers_psalm text,
        vespers_gospel text,
        matins_psalm text,
        matins_gospel text,
        liturgy_pauline text,
        liturgy_catholic text,
        liturgy_acts text,
        liturgy_psalm text,
        liturgy_gospel text,
        synaxarium text,
        feast_name varchar(255),
        feast_type varchar(50),
        PRIMARY KEY (id),
        KEY gregorian_date (gregorian_date)
    ) $charset_collate;";
    
    // جدول الأعياد والمناسبات
    $table_feasts = $wpdb->prefix . 'katamars_feasts';
    $sql_feasts = "CREATE TABLE $table_feasts (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        coptic_date varchar(50),
        gregorian_date date,
        feast_type varchar(50),
        description text,
        special_readings text,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_readings);
    dbDelta($sql_feasts);
}

// إدراج البيانات الأولية
function wp_katamars_insert_initial_data() {
    // سيتم إضافة البيانات الأولية هنا
}
?>
