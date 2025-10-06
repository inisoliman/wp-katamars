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

        // جدول القراءات
        $table_readings = $wpdb->prefix . 'katamars_readings';
        $sql_readings = "CREATE TABLE $table_readings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            date_coptic varchar(10) NOT NULL,
            date_gregorian date NOT NULL,
            service_type varchar(20) NOT NULL,
            reading_type varchar(50) NOT NULL,
            book varchar(100) NOT NULL,
            chapter_start int NOT NULL,
            verse_start int NOT NULL,
            chapter_end int NOT NULL,
            verse_end int NOT NULL,
            text_arabic longtext NOT NULL,
            text_english longtext,
            reference varchar(200) NOT NULL,
            season varchar(100) DEFAULT '',
            fast_type varchar(50) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY date_service (date_gregorian, service_type),
            KEY season_fast (season, fast_type),
            KEY coptic_date (date_coptic)
        ) $charset_collate;";

        // جدول السنكسار
        $table_synaxarium = $wpdb->prefix . 'katamars_synaxarium';
        $sql_synaxarium = "CREATE TABLE $table_synaxarium (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            month_coptic tinyint(2) NOT NULL,
            day_coptic tinyint(2) NOT NULL,
            saint_name_ar varchar(255) NOT NULL,
            saint_name_en varchar(255),
            saint_type varchar(50) NOT NULL,
            story_ar longtext NOT NULL,
            story_en longtext,
            commemoration_ar text,
            commemoration_en text,
            image_url varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY coptic_date (month_coptic, day_coptic),
            KEY saint_type (saint_type),
            KEY saint_name (saint_name_ar(100))
        ) $charset_collate;";

        // جدول الأعياد
        $table_feasts = $wpdb->prefix . 'katamars_feasts';
        $sql_feasts = "CREATE TABLE $table_feasts (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            feast_name_ar varchar(255) NOT NULL,
            feast_name_en varchar(255),
            feast_type varchar(50) NOT NULL,
            date_type varchar(20) NOT NULL,
            month_coptic tinyint(2),
            day_coptic tinyint(2),
            date_gregorian date,
            duration_days tinyint(3) DEFAULT 1,
            rank_level tinyint(1) DEFAULT 1,
            description_ar text,
            description_en text,
            special_readings boolean DEFAULT FALSE,
            fast_breaking boolean DEFAULT FALSE,
            icon_name varchar(100),
            color_theme varchar(20) DEFAULT 'gold',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY feast_type (feast_type),
            KEY coptic_date (month_coptic, day_coptic),
            KEY gregorian_date (date_gregorian),
            KEY rank_level (rank_level)
        ) $charset_collate;";

        // جدول القديسين
        $table_saints = $wpdb->prefix . 'katamars_saints';
        $sql_saints = "CREATE TABLE $table_saints (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            saint_name_ar varchar(255) NOT NULL,
            saint_name_en varchar(255),
            saint_name_coptic varchar(255),
            saint_title_ar varchar(255),
            saint_title_en varchar(255),
            birth_date varchar(50),
            death_date varchar(50),
            feast_day varchar(20),
            biography_ar longtext,
            biography_en longtext,
            miracles_ar text,
            miracles_en text,
            relics_location varchar(255),
            patron_of text,
            image_url varchar(500),
            icon_url varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY saint_name (saint_name_ar(100)),
            KEY feast_day (feast_day),
            FULLTEXT (saint_name_ar, biography_ar, miracles_ar)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        dbDelta($sql_readings);
        dbDelta($sql_synaxarium);
        dbDelta($sql_feasts);
        dbDelta($sql_saints);

        update_option('katamars_db_version', '1.0');
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
            "SELECT * FROM $table 
             WHERE date_gregorian = %s 
             AND service_type = %s 
             ORDER BY reading_type",
            $date,
            $service
        ));

        return $results;
    }

    /**
     * الحصول على السنكسار اليومي
     */
    public static function get_daily_synaxarium($coptic_month, $coptic_day) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'katamars_synaxarium';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
             WHERE month_coptic = %d 
             AND day_coptic = %d 
             ORDER BY saint_type, saint_name_ar",
            $coptic_month,
            $coptic_day
        ));

        return $results;
    }
}