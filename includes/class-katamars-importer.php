<?php
/**
 * كلاس استيراد قاعدة البيانات القديمة
 * 
 * @package Katamars
 * @since 2.0.0
 */

class Katamars_Importer {

    /**
     * استيراد قاعدة البيانات القديمة
     */
    public static function import_old_database($file_path) {
        global $wpdb;
        
        if (!file_exists($file_path)) {
            return new WP_Error('file_not_found', 'ملف SQL غير موجود');
        }

        // قراءة محتوى الملف
        $sql_content = file_get_contents($file_path);
        if ($sql_content === false) {
            return new WP_Error('file_read_error', 'خطأ في قراءة الملف');
        }

        // تقسيم الاستعلامات
        $queries = self::parse_sql_file($sql_content);
        
        $imported = 0;
        $errors = [];
        $log = [];

        // تنفيذ الاستعلامات
        foreach ($queries as $i => $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            // معالجة الاستعلام
            $processed_query = self::process_query($query);
            
            if ($processed_query === false) {
                continue; // تخطي الاستعلامات غير المدعومة
            }
            
            $result = $wpdb->query($processed_query);
            
            if ($result === false) {
                $errors[] = [
                    'query_num' => $i + 1,
                    'error' => $wpdb->last_error,
                    'query' => substr($query, 0, 100) . '...'
                ];
            } else {
                $imported++;
                $log[] = "تم تنفيذ الاستعلام #" . ($i + 1);
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
            'log' => $log,
            'total_queries' => count($queries)
        ];
    }

    /**
     * تحليل ملف SQL
     */
    private static function parse_sql_file($content) {
        // إزالة التعليقات
        $content = preg_replace('/^--.*$/m', '', $content);
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        
        // تقسيم على semicolon
        $queries = explode(';', $content);
        
        return array_filter(array_map('trim', $queries));
    }

    /**
     * معالجة الاستعلام
     */
    private static function process_query($query) {
        global $wpdb;
        
        // تخطي إنشاء قاعدة البيانات
        if (stripos($query, 'CREATE DATABASE') !== false) {
            return false;
        }
        
        if (stripos($query, 'USE ') === 0) {
            return false;
        }
        
        // استبدال اسم قاعدة البيانات القديمة
        $query = str_replace('u626751827_katamars.', $wpdb->prefix, $query);
        $query = preg_replace('/`u626751827_katamars`\./', $wpdb->prefix, $query);
        
        // استبدال أسماء الجداول
        $old_tables = [
            'katamars_readings' => $wpdb->prefix . 'katamars_readings',
            'katamars_synaxarium' => $wpdb->prefix . 'katamars_synaxarium', 
            'katamars_feasts' => $wpdb->prefix . 'katamars_feasts',
            'katamars_saints' => $wpdb->prefix . 'katamars_saints',
            'katamars_calendar' => $wpdb->prefix . 'katamars_calendar'
        ];
        
        foreach ($old_tables as $old => $new) {
            $query = str_replace("`$old`", "`$new`", $query);
            $query = str_replace("$old ", "$new ", $query);
        }
        
        return $query;
    }

    /**
     * تحميل ملف SQL من GitHub
     */
    public static function download_sql_from_github() {
        $github_url = 'https://raw.githubusercontent.com/inisoliman/katamars/main/u626751827_katamars.sql';
        
        $response = wp_remote_get($github_url, [
            'timeout' => 30,
            'user-agent' => 'Katamars-WordPress-Plugin/2.0'
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return new WP_Error('empty_response', 'ملف SQL فارغ أو غير موجود');
        }
        
        // حفظ الملف محلياً
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/katamars_old_database.sql';
        
        if (file_put_contents($file_path, $body) === false) {
            return new WP_Error('file_save_error', 'خطأ في حفظ الملف');
        }
        
        return $file_path;
    }

    /**
     * التحقق من سلامة البيانات بعد الاستيراد
     */
    public static function verify_import() {
        global $wpdb;
        
        $tables = [
            'readings' => $wpdb->prefix . 'katamars_readings',
            'synaxarium' => $wpdb->prefix . 'katamars_synaxarium',
            'feasts' => $wpdb->prefix . 'katamars_feasts',
            'saints' => $wpdb->prefix . 'katamars_saints'
        ];
        
        $stats = [];
        
        foreach ($tables as $name => $table) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM `$table`");
            $stats[$name] = $count ? intval($count) : 0;
        }
        
        return $stats;
    }

    /**
     * تنظيف البيانات القديمة قبل الاستيراد
     */
    public static function cleanup_before_import() {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'katamars_readings',
            $wpdb->prefix . 'katamars_synaxarium', 
            $wpdb->prefix . 'katamars_feasts',
            $wpdb->prefix . 'katamars_saints'
        ];
        
        foreach ($tables as $table) {
            $wpdb->query("TRUNCATE TABLE `$table`");
        }
        
        return true;
    }

    /**
     * إصلاح التشفير العربي
     */
    public static function fix_arabic_encoding() {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'katamars_readings',
            $wpdb->prefix . 'katamars_synaxarium',
            $wpdb->prefix . 'katamars_feasts', 
            $wpdb->prefix . 'katamars_saints'
        ];
        
        foreach ($tables as $table) {
            $wpdb->query("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        
        return true;
    }

    /**
     * إنشاء نسخة احتياطية قبل الاستيراد
     */
    public static function create_backup() {
        global $wpdb;
        
        $upload_dir = wp_upload_dir();
        $backup_dir = $upload_dir['basedir'] . '/katamars_backups';
        
        if (!file_exists($backup_dir)) {
            wp_mkdir_p($backup_dir);
        }
        
        $backup_file = $backup_dir . '/katamars_backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        $tables = [
            $wpdb->prefix . 'katamars_readings',
            $wpdb->prefix . 'katamars_synaxarium',
            $wpdb->prefix . 'katamars_feasts',
            $wpdb->prefix . 'katamars_saints'
        ];
        
        $backup_content = "-- Katamars Backup " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            // التحقق من وجود الجدول
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
            if (!$table_exists) continue;
            
            $backup_content .= "-- Table: $table\n";
            
            // الحصول على بنية الجدول
            $create_table = $wpdb->get_row("SHOW CREATE TABLE `$table`", ARRAY_N);
            if ($create_table) {
                $backup_content .= $create_table[1] . ";\n\n";
            }
            
            // الحصول على البيانات
            $rows = $wpdb->get_results("SELECT * FROM `$table`", ARRAY_A);
            
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    $values[] = "'" . $wpdb->_escape($value) . "'";
                }
                $backup_content .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
            }
            
            $backup_content .= "\n";
        }
        
        if (file_put_contents($backup_file, $backup_content) !== false) {
            return $backup_file;
        }
        
        return false;
    }
}