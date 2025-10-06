<?php
/**
 * كلاس تفعيل الإضافة
 */

class Katamars_Activator {

    /**
     * تفعيل الإضافة
     */
    public static function activate() {
        // إنشاء قواعد البيانات
        self::create_database_tables();
        
        // إضافة البيانات الافتراضية
        self::insert_default_data();
        
        // إعداد الخيارات
        self::set_default_options();
        
        // تحديث الكاش
        flush_rewrite_rules();
        
        // حفظ رقم الإصدار
        update_option('katamars_version', KATAMARS_VERSION);
        update_option('katamars_activation_date', current_time('mysql'));
    }

    /**
     * إنشاء الجداول
     */
    private static function create_database_tables() {
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-database.php';
        Katamars_Database::create_tables();
    }

    /**
     * إدراج البيانات الافتراضية
     */
    private static function insert_default_data() {
        // سيتم إضافة البيانات من ملفات JSON
        self::import_readings_data();
        self::import_synaxarium_data();
        self::import_feasts_data();
    }

    /**
     * استيراد بيانات القراءات
     */
    private static function import_readings_data() {
        $readings_file = KATAMARS_PLUGIN_DIR . 'data/readings.json';
        if (file_exists($readings_file)) {
            $readings = json_decode(file_get_contents($readings_file), true);
            // كود استيراد القراءات
        }
    }

    /**
     * استيراد بيانات السنكسار
     */
    private static function import_synaxarium_data() {
        $synax_file = KATAMARS_PLUGIN_DIR . 'data/synaxarium.json';
        if (file_exists($synax_file)) {
            $synaxarium = json_decode(file_get_contents($synax_file), true);
            // كود استيراد السنكسار
        }
    }

    /**
     * استيراد بيانات الأعياد
     */
    private static function import_feasts_data() {
        $feasts_file = KATAMARS_PLUGIN_DIR . 'data/feasts.json';
        if (file_exists($feasts_file)) {
            $feasts = json_decode(file_get_contents($feasts_file), true);
            // كود استيراد الأعياد
        }
    }

    /**
     * إعداد الخيارات الافتراضية
     */
    private static function set_default_options() {
        $default_settings = [
            'language' => 'ar',
            'show_coptic_date' => true,
            'show_synaxarium' => true,
            'default_service' => 'liturgy',
            'cache_duration' => 86400,
            'enable_api' => true,
            'rtl_support' => true
        ];
        
        add_option('katamars_settings', $default_settings);
    }
}