<?php
/**
 * كلاس إدارة قاعدة البيانات
 */

class Katamars_Database {

    /**
     * إنشاء الجداول
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $success = true;

        // جدول القراءات
        $table_readings = $wpdb->prefix . 'katamars_readings';
        $sql = "CREATE TABLE IF NOT EXISTS {$table_readings} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            date_coptic varchar(10) NOT NULL DEFAULT '',
            date_gregorian date NOT NULL,
            service_type varchar(20) NOT NULL DEFAULT '',
            reading_type varchar(50) NOT NULL DEFAULT '',
            book varchar(100) NOT NULL DEFAULT '',
            chapter_start int NOT NULL DEFAULT 0,
            verse_start int NOT NULL DEFAULT 0,
            chapter_end int NOT NULL DEFAULT 0,
            verse_end int NOT NULL DEFAULT 0,
            text_arabic longtext NOT NULL,
            text_english longtext,
            reference varchar(200) NOT NULL DEFAULT '',
            season varchar(100) DEFAULT '',
            fast_type varchar(50) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY date_gregorian (date_gregorian),
            KEY service_type (service_type),
            KEY date_coptic (date_coptic)
        ) {$charset_collate}";
        
        $result = $wpdb->query($sql);
        if ($result === false) $success = false;

        // جدول السنكسار
        $table_synaxarium = $wpdb->prefix . 'katamars_synaxarium';
        $sql = "CREATE TABLE IF NOT EXISTS {$table_synaxarium} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            month_coptic tinyint(2) NOT NULL DEFAULT 0,
            day_coptic tinyint(2) NOT NULL DEFAULT 0,
            saint_name_ar varchar(255) NOT NULL DEFAULT '',
            saint_name_en varchar(255) DEFAULT NULL,
            saint_type varchar(50) NOT NULL DEFAULT '',
            story_ar longtext NOT NULL,
            story_en longtext,
            commemoration_ar text,
            commemoration_en text,
            image_url varchar(500) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY month_coptic (month_coptic),
            KEY day_coptic (day_coptic),
            KEY saint_type (saint_type)
        ) {$charset_collate}";
        
        $result = $wpdb->query($sql);
        if ($result === false) $success = false;

        // جدول الأعياد
        $table_feasts = $wpdb->prefix . 'katamars_feasts';
        $sql = "CREATE TABLE IF NOT EXISTS {$table_feasts} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            feast_name_ar varchar(255) NOT NULL DEFAULT '',
            feast_name_en varchar(255) DEFAULT NULL,
            feast_type varchar(50) NOT NULL DEFAULT '',
            date_type varchar(20) NOT NULL DEFAULT '',
            month_coptic tinyint(2) DEFAULT NULL,
            day_coptic tinyint(2) DEFAULT NULL,
            date_gregorian date DEFAULT NULL,
            duration_days tinyint(3) DEFAULT 1,
            rank_level tinyint(1) DEFAULT 1,
            description_ar text,
            description_en text,
            special_readings tinyint(1) DEFAULT 0,
            fast_breaking tinyint(1) DEFAULT 0,
            icon_name varchar(100) DEFAULT NULL,
            color_theme varchar(20) DEFAULT 'gold',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY feast_type (feast_type),
            KEY date_gregorian (date_gregorian),
            KEY rank_level (rank_level)
        ) {$charset_collate}";
        
        $result = $wpdb->query($sql);
        if ($result === false) $success = false;

        // جدول القديسين
        $table_saints = $wpdb->prefix . 'katamars_saints';
        $sql = "CREATE TABLE IF NOT EXISTS {$table_saints} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            saint_name_ar varchar(255) NOT NULL DEFAULT '',
            saint_name_en varchar(255) DEFAULT NULL,
            saint_name_coptic varchar(255) DEFAULT NULL,
            saint_title_ar varchar(255) DEFAULT NULL,
            saint_title_en varchar(255) DEFAULT NULL,
            birth_date varchar(50) DEFAULT NULL,
            death_date varchar(50) DEFAULT NULL,
            feast_day varchar(20) DEFAULT NULL,
            biography_ar longtext,
            biography_en longtext,
            miracles_ar text,
            miracles_en text,
            relics_location varchar(255) DEFAULT NULL,
            patron_of text,
            image_url varchar(500) DEFAULT NULL,
            icon_url varchar(500) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY feast_day (feast_day)
        ) {$charset_collate}";
        
        $result = $wpdb->query($sql);
        if ($result === false) $success = false;

        if ($success) {
            update_option('katamars_db_version', '1.0');
        }
        
        return $success;
    }

    /**
     * الحصول على القراءات اليومية
     */
    public static function get_daily_readings($date = null, $service = 'liturgy') {
        global $wpdb;
        
        if (!$date) {
            $date = current_time('Y-m-d');
        }

        $table = $wpdb->prefix . 'katamars_readings';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE date_gregorian = %s AND service_type = %s ORDER BY reading_type",
            $date,
            $service
        ));

        return $results ? $results : array();
    }

    /**
     * الحصول على السنكسار اليومي
     */
    public static function get_daily_synaxarium($coptic_month, $coptic_day) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'katamars_synaxarium';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE month_coptic = %d AND day_coptic = %d ORDER BY saint_type, saint_name_ar",
            $coptic_month,
            $coptic_day
        ));

        return $results ? $results : array();
    }
}