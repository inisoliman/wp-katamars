<?php
/**
 * ملف حذف الإضافة
 * يتم تنفيذه عند حذف الإضافة نهائياً
 */

// التأكد من أن الملف يتم استدعاؤه من ووردبريس
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// حذف الجداول
$tables = [
    $wpdb->prefix . 'katamars_readings',
    $wpdb->prefix . 'katamars_synaxarium',
    $wpdb->prefix . 'katamars_feasts',
    $wpdb->prefix . 'katamars_calendar',
    $wpdb->prefix . 'katamars_saints'
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
}

// حذف جميع الخيارات
delete_option('katamars_version');
delete_option('katamars_settings');
delete_option('katamars_db_version');
delete_option('katamars_last_update');

// حذف جميع الـ Transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_katamars_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_katamars_%'");

// حذف User Meta المخصصة
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'katamars_%'");

// مسح الكاش
wp_cache_flush();