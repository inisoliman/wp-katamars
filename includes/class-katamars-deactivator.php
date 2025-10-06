<?php
/**
 * كلاس إلغاء تفعيل الإضافة
 */

class Katamars_Deactivator {

    /**
     * إلغاء تفعيل الإضافة
     */
    public static function deactivate() {
        // مسح الكاش
        self::clear_cache();
        
        // إزالة الـ Cron Jobs
        self::clear_scheduled_events();
        
        // تنظيف Rewrite Rules
        flush_rewrite_rules();
    }

    /**
     * مسح الكاش
     */
    private static function clear_cache() {
        global $wpdb;
        
        // حذف Transients
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_katamars_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_katamars_%'");
        
        // مسح Object Cache
        wp_cache_flush();
    }

    /**
     * إزالة المهام المجدولة
     */
    private static function clear_scheduled_events() {
        wp_clear_scheduled_hook('katamars_daily_update');
        wp_clear_scheduled_hook('katamars_cache_cleanup');
    }
}