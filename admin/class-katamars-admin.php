<?php
/**
 * كلاس الإدارة
 */

class Katamars_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * تحميل ملفات CSS للإدارة
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            KATAMARS_PLUGIN_URL . 'admin/css/katamars-admin.css',
            [],
            $this->version,
            'all'
        );
    }

    /**
     * تحميل ملفات JavaScript للإدارة
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            KATAMARS_PLUGIN_URL . 'admin/js/katamars-admin.js',
            ['jquery'],
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'katamars_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('katamars_admin_nonce')
        ]);
    }

    /**
     * إضافة قائمة الإدارة
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('القطمارس القبطي', 'katamars'),
            __('القطمارس', 'katamars'),
            'manage_options',
            $this->plugin_name,
            [$this, 'display_plugin_admin_page'],
            'dashicons-book-alt',
            25
        );

        add_submenu_page(
            $this->plugin_name,
            __('لوحة التحكم', 'katamars'),
            __('لوحة التحكم', 'katamars'),
            'manage_options',
            $this->plugin_name,
            [$this, 'display_plugin_admin_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('استيراد البيانات', 'katamars'),
            __('استيراد البيانات', 'katamars'),
            'manage_options',
            $this->plugin_name . '-import',
            [$this, 'display_import_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('القراءات اليومية', 'katamars'),
            __('القراءات', 'katamars'),
            'manage_options',
            $this->plugin_name . '-readings',
            [$this, 'display_readings_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('السنكسار', 'katamars'),
            __('السنكسار', 'katamars'),
            'manage_options',
            $this->plugin_name . '-synaxarium',
            [$this, 'display_synaxarium_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('الأعياد والمناسبات', 'katamars'),
            __('الأعياد', 'katamars'),
            'manage_options',
            $this->plugin_name . '-feasts',
            [$this, 'display_feasts_page']
        );

        add_submenu_page(
            $this->plugin_name,
            __('الإعدادات', 'katamars'),
            __('الإعدادات', 'katamars'),
            'manage_options',
            $this->plugin_name . '-settings',
            [$this, 'display_settings_page']
        );
    }

    /**
     * عرض الصفحة الرئيسية للإدارة
     */
    public function display_plugin_admin_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-dashboard.php';
    }

    /**
     * عرض صفحة استيراد البيانات
     */
    public function display_import_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-import.php';
    }

    /**
     * عرض صفحة القراءات
     */
    public function display_readings_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-readings.php';
    }

    /**
     * عرض صفحة السنكسار
     */
    public function display_synaxarium_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-synaxarium.php';
    }

    /**
     * عرض صفحة الأعياد
     */
    public function display_feasts_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-feasts.php';
    }

    /**
     * عرض صفحة الإعدادات
     */
    public function display_settings_page() {
        include_once KATAMARS_PLUGIN_DIR . 'admin/views/admin-settings.php';
    }

    /**
     * تهيئة الإعدادات
     */
    public function init_settings() {
        register_setting(
            $this->plugin_name,
            $this->plugin_name . '_settings'
        );
    }

    /**
     * AJAX: الحصول على إحصائيات البيانات
     */
    public function ajax_get_stats() {
        check_ajax_referer('katamars_admin_nonce', 'nonce');
        
        global $wpdb;
        
        $stats = [
            'readings' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_readings"),
            'synaxarium' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_synaxarium"),
            'feasts' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_feasts"),
            'saints' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}katamars_saints")
        ];
        
        wp_send_json_success($stats);
    }

    /**
     * AJAX: حفظ الإعدادات
     */
    public function ajax_save_settings() {
        check_ajax_referer('katamars_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('عدم وجود صلاحية');
        }
        
        // حفظ الإعدادات
        $settings = [];
        
        if (isset($_POST['katamars_settings'])) {
            $settings = $_POST['katamars_settings'];
            update_option('katamars_settings', $settings);
        }
        
        wp_send_json_success('تم حفظ الإعدادات');
    }

    /**
     * AJAX: تحديث القراءات
     */
    public function ajax_update_readings() {
        check_ajax_referer('katamars_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('عدم وجود صلاحية');
        }
        
        // تحديث القراءات من مصدر خارجي أو API
        // هذه وظيفة مستقبلية
        
        wp_send_json_success('تم تحديث القراءات');
    }

    /**
     * AJAX: استيراد البيانات
     */
    public function ajax_import_data() {
        check_ajax_referer('katamars_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('عدم وجود صلاحية');
        }
        
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-importer.php';
        
        // التحقق من وجود ملف مرفوع
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error('يرجى اختيار ملف صحيح');
        }
        
        $uploaded_file = $_FILES['import_file']['tmp_name'];
        $result = Katamars_Importer::import_old_database($uploaded_file);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success($result);
    }

    /**
     * AJAX: تصدير البيانات
     */
    public function ajax_export_data() {
        check_ajax_referer('katamars_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('عدم وجود صلاحية');
        }
        
        require_once KATAMARS_PLUGIN_DIR . 'includes/class-katamars-importer.php';
        
        $backup_file = Katamars_Importer::create_backup();
        
        if ($backup_file && file_exists($backup_file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="katamars_export_' . date('Y-m-d_H-i-s') . '.sql"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file));
            
            readfile($backup_file);
            unlink($backup_file); // حذف الملف المؤقت
            exit;
        } else {
            wp_die('خطأ في إنشاء ملف الرزمة');
        }
    }

    /**
     * تسجيل AJAX actions
     */
    public function register_ajax_actions() {
        add_action('wp_ajax_katamars_get_stats', [$this, 'ajax_get_stats']);
        add_action('wp_ajax_katamars_save_settings', [$this, 'ajax_save_settings']);
        add_action('wp_ajax_katamars_update_readings', [$this, 'ajax_update_readings']);
        add_action('wp_ajax_katamars_import_data', [$this, 'ajax_import_data']);
        add_action('wp_ajax_katamars_export_data', [$this, 'ajax_export_data']);
    }
}